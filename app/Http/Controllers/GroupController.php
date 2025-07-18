<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupExpense;
use App\Models\GroupExpenseSplit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GroupController extends Controller
{
    /**
     * Display all groups
     */
    public function index()
    {
        $groups = Group::with(['members', 'expenses'])
            ->where('is_active', true)
            ->latest()
            ->get();

        return view('groups.index', compact('groups'));
    }

    /**
     * Show create group form
     */
    public function create()
    {
        return view('groups.create');
    }

    /**
     * Store a new group
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'members' => 'required|array|min:1',
            'members.*.name' => 'required|string|max:255',
            'members.*.email' => 'nullable|email|max:255',
            'members.*.phone' => 'nullable|string|max:15'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('group-images', 'public');
        }

        $group = Group::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imagePath,
            'created_by' => Auth::id() ?? 1 // Use Auth::id() or default to 1
        ]);

        // Add creator as admin
        $group->members()->create([
            'user_id' => Auth::id() ?? 1,
            'name' => Auth::user()->name ?? 'Admin',
            'email' => Auth::user()->email ?? null,
            'role' => 'admin',
            'status' => 'active',
            'joined_at' => now()
        ]);

        // Add other members
        foreach ($request->members as $memberData) {
            $group->members()->create([
                'name' => $memberData['name'],
                'email' => $memberData['email'] ?? null,
                'phone' => $memberData['phone'] ?? null,
                'role' => 'member',
                'status' => 'active',
                'joined_at' => now()
            ]);
        }

        return redirect()->route('groups.show', $group)
            ->with('success', 'Group created successfully!');
    }

    /**
     * Display group details
     */
    public function show(Group $group)
    {
        $group->load(['members', 'expenses.paidBy', 'expenses.splits.member']);

        $recentExpenses = $group->getRecentExpenses(10);
        $balances = $group->calculateBalances();
        $totalExpenses = $group->total_expenses;
        $memberCount = $group->member_count;

        $categories = [
            'Food & Dining',
            'Transportation',
            'Shopping',
            'Entertainment',
            'Health & Medical',
            'Bills & Utilities',
            'Education',
            'Travel',
            'Accommodation',
            'Other'
        ];

        return view('groups.show', compact(
            'group',
            'recentExpenses',
            'balances',
            'totalExpenses',
            'memberCount',
            'categories'
        ));
    }

    /**
     * Show edit group form
     */
    public function edit(Group $group)
    {
        $group->load('members');
        return view('groups.edit', compact('group'));
    }

    /**
     * Update group
     */
    public function update(Request $request, Group $group)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description
        ];

        if ($request->hasFile('image')) {
            if ($group->image) {
                Storage::disk('public')->delete($group->image);
            }
            $data['image'] = $request->file('image')->store('group-images', 'public');
        }

        $group->update($data);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Group updated successfully!');
    }

    /**
     * Delete group
     */
    public function destroy(Group $group)
    {
        if ($group->image) {
            Storage::disk('public')->delete($group->image);
        }

        $group->delete();

        return redirect()->route('groups.index')
            ->with('success', 'Group deleted successfully!');
    }

    /**
     * Add member to group
     */
    public function addMember(Request $request, Group $group)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:15'
        ]);

        $group->members()->create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => 'member',
            'status' => 'active',
            'joined_at' => now()
        ]);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Member added successfully!');
    }

    /**
     * Remove member from group
     */
    public function removeMember(Group $group, GroupMember $member)
    {
        $member->update(['status' => 'inactive']);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Member removed successfully!');
    }

    /**
     * Store group expense
     */
    public function storeExpense(Request $request, Group $group)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'category' => 'required|string',
            'paid_by' => 'required|exists:group_members,id',
            'expense_date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'payment_method' => 'nullable|string|max:50',
            'split_type' => 'required|in:equal,exact,percentage',
            'splits' => 'required|array|min:1',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt_image')) {
            $receiptPath = $request->file('receipt_image')->store('receipts', 'public');
        }

        $expense = $group->expenses()->create([
            'paid_by' => $request->paid_by,
            'title' => $request->title,
            'description' => $request->description,
            'amount' => $request->amount,
            'category' => $request->category,
            'expense_date' => $request->expense_date,
            'payment_method' => $request->payment_method,
            'receipt_image' => $receiptPath
        ]);

        // Handle splits based on type
        if ($request->split_type === 'equal') {
            $expense->splitEqually($request->splits);
        } elseif ($request->split_type === 'exact') {
            $expense->splitExactly($request->splits);
        } elseif ($request->split_type === 'percentage') {
            $expense->splitByPercentage($request->splits);
        }

        return redirect()->route('groups.show', $group)
            ->with('success', 'Expense added successfully!');
    }

    /**
     * Show expense details
     */
    public function showExpense(Group $group, GroupExpense $expense)
    {
        $expense->load(['paidBy', 'splits.member']);
        return view('groups.expense-details', compact('group', 'expense'));
    }

    /**
     * Delete expense
     */
    public function deleteExpense(Group $group, GroupExpense $expense)
    {
        if ($expense->receipt_image) {
            Storage::disk('public')->delete($expense->receipt_image);
        }

        $expense->delete();

        return redirect()->route('groups.show', $group)
            ->with('success', 'Expense deleted successfully!');
    }

    /**
     * Settle expense split
     */
    public function settleSplit(Group $group, GroupExpenseSplit $split)
    {
        $split->markAsPaid();

        return redirect()->back()
            ->with('success', 'Split settled successfully!');
    }

    /**
     * Get group statistics
     */
    public function statistics(Group $group)
    {
        // Basic statistics
        $totalExpenses = $group->expenses()->sum('amount');
        $memberCount = $group->activeMembers()->count();
        $totalTransactions = $group->expenses()->count();
        $averageExpense = $totalTransactions > 0 ? $totalExpenses / $totalTransactions : 0;

        // Category breakdown
        $categoryBreakdown = $group->expenses()
            ->select('category')
            ->selectRaw('SUM(amount) as total')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('category')
            ->get()
            ->map(function ($item) use ($totalExpenses) {
                $item->percentage = $totalExpenses > 0 ? ($item->total / $totalExpenses) * 100 : 0;
                $item->name = $item->category;
                // Add default colors and icons - you can customize these
                $item->color = $this->getCategoryColor($item->category);
                $item->icon = $this->getCategoryIcon($item->category);
                return $item;
            });

        // Member spending analysis
        $memberSpending = $group->activeMembers()->get()->map(function ($member) use ($group) {
            $totalPaid = $group->expenses()->where('paid_by', $member->id)->sum('amount');

            // Calculate total owes (you'll need to implement this based on your expense splits logic)
            $totalOwes = $this->calculateMemberOwes($member, $group);

            $member->total_paid = $totalPaid;
            $member->total_owes = $totalOwes;
            $member->balance = $totalPaid - $totalOwes;

            return $member;
        });

        // Monthly trend
        $monthlyTrend = $group->expenses()
            ->selectRaw('YEAR(expense_date) as year, MONTH(expense_date) as month')
            ->selectRaw('SUM(amount) as total_amount')
            ->selectRaw('COUNT(*) as transaction_count')
            ->selectRaw('AVG(amount) as average_amount')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get()
            ->map(function ($item) {
                $item->month_name = date('F Y', mktime(0, 0, 0, $item->month, 1, $item->year));
                return $item;
            });

        // Payment methods
        $paymentMethods = $group->expenses()
            ->select('payment_method')
            ->selectRaw('SUM(amount) as total_amount')
            ->selectRaw('COUNT(*) as usage_count')
            ->whereNotNull('payment_method')
            ->where('payment_method', '!=', '')
            ->groupBy('payment_method')
            ->get()
            ->map(function ($item) {
                $item->method = $item->payment_method;
                $item->icon = $this->getPaymentMethodIcon($item->payment_method);
                return $item;
            });

        // Recent activity
        $recentActivity = $group->expenses()
            ->select('title', 'description', 'created_at')
            ->selectRaw("'expense' as type")
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Additional statistics
        $highestExpense = $group->expenses()->max('amount') ?? 0;
        $lowestExpense = $group->expenses()->min('amount') ?? 0;

        $mostActiveMonth = $group->expenses()
            ->selectRaw('YEAR(expense_date) as year, MONTH(expense_date) as month')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('count', 'desc')
            ->first();

        $mostActiveMonth = $mostActiveMonth ?
            date('F Y', mktime(0, 0, 0, $mostActiveMonth->month, 1, $mostActiveMonth->year)) :
            null;

        $lastExpense = $group->expenses()->orderBy('created_at', 'desc')->first();
        $daysSinceLastExpense = $lastExpense ?
            now()->diffInDays($lastExpense->created_at) :
            null;

        return view('groups.statistics', compact(
            'group',
            'totalExpenses',
            'memberCount',
            'totalTransactions',
            'averageExpense',
            'categoryBreakdown',
            'memberSpending',
            'monthlyTrend',
            'paymentMethods',
            'recentActivity',
            'highestExpense',
            'lowestExpense',
            'mostActiveMonth',
            'daysSinceLastExpense'
        ));
    }

    // Helper methods
    private function getCategoryColor($category)
    {
        $colors = [
            'Food' => '#ff6b6b',
            'Transportation' => '#4ecdc4',
            'Entertainment' => '#45b7d1',
            'Shopping' => '#f39c12',
            'Bills' => '#9b59b6',
            'Travel' => '#e74c3c',
            'Health' => '#2ecc71',
            'Education' => '#3498db',
            'Others' => '#95a5a6'
        ];

        return $colors[$category] ?? '#007bff';
    }

    private function getCategoryIcon($category)
    {
        $icons = [
            'Food' => 'fas fa-utensils',
            'Transportation' => 'fas fa-car',
            'Entertainment' => 'fas fa-film',
            'Shopping' => 'fas fa-shopping-cart',
            'Bills' => 'fas fa-file-invoice',
            'Travel' => 'fas fa-plane',
            'Health' => 'fas fa-heartbeat',
            'Education' => 'fas fa-graduation-cap',
            'Others' => 'fas fa-circle'
        ];

        return $icons[$category] ?? 'fas fa-circle';
    }

    private function getPaymentMethodIcon($method)
    {
        $icons = [
            'Cash' => 'money-bill',
            'Card' => 'credit-card',
            'UPI' => 'mobile-alt',
            'Net Banking' => 'university'
        ];

        return $icons[$method] ?? 'circle';
    }

    private function calculateMemberOwes($member, $group)
    {
        // This is a simplified calculation - you'll need to implement this based on your expense splits logic
        // For now, returning a basic calculation
        $totalGroupExpenses = $group->expenses()->sum('amount');
        $memberCount = $group->activeMembers()->count();

        return $memberCount > 0 ? $totalGroupExpenses / $memberCount : 0;
    }
}
