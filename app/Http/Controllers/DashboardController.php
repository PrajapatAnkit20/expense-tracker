<?php
// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Category;
use App\Models\PersonalExpense;
use App\Models\GroupExpense;
use App\Models\GroupExpenseSplit;
use App\Models\GroupMember;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Personal expenses this month
        $personalExpensesThisMonth = PersonalExpense::where('user_id', $userId)
            ->whereMonth('expense_date', $currentMonth)
            ->whereYear('expense_date', $currentYear)
            ->sum('amount');

        // Group expenses this month (where user is a member)
        $groupExpensesThisMonth = GroupExpense::whereHas('group.members', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->whereMonth('expense_date', $currentMonth)
            ->whereYear('expense_date', $currentYear)
            ->sum('amount');

        // Total expenses this month (personal + group)
        $thisMonthTotal = $personalExpensesThisMonth + $groupExpensesThisMonth;

        // Budget calculation (you can set this as a user preference or calculate based on previous months)
        $monthlyBudget = 15000; // You can make this dynamic
        $budgetLeft = $monthlyBudget - $thisMonthTotal;

        // Amount user owes in group expenses
        $userOwes = GroupExpenseSplit::whereHas('member', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->where('is_paid', false)
            ->sum('amount');

        // Recent personal expenses
        $recentPersonalExpenses = PersonalExpense::where('user_id', $userId)
            ->orderBy('expense_date', 'desc')
            ->limit(3)
            ->get();

        // Recent group expenses
        $recentGroupExpenses = GroupExpense::with(['group', 'paidByMember'])
            ->whereHas('group.members', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orderBy('expense_date', 'desc')
            ->limit(3)
            ->get();

        // Get user's share for each group expense
        $recentGroupExpensesWithShares = $recentGroupExpenses->map(function ($expense) use ($userId) {
            $userShare = GroupExpenseSplit::whereHas('member', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
                ->where('group_expense_id', $expense->id)
                ->first();

            $expense->user_share = $userShare ? $userShare->amount : 0;
            return $expense;
        });

        // Combine recent activities (personal + group)
        $recentActivities = collect();

        foreach ($recentPersonalExpenses as $expense) {
            $recentActivities->push([
                'type' => 'personal',
                'title' => $expense->description,
                'subtitle' => 'Personal expense',
                'amount' => $expense->amount,
                'user_share' => $expense->amount,
                'category' => $expense->category,
                'date' => $expense->expense_date,
                'icon' => $this->getCategoryIcon($expense->category)
            ]);
        }

        foreach ($recentGroupExpensesWithShares as $expense) {
            $recentActivities->push([
                'type' => 'group',
                'title' => $expense->title,
                'subtitle' => 'Group: ' . $expense->group->name,
                'amount' => $expense->amount,
                'user_share' => $expense->user_share,
                'category' => $expense->category,
                'date' => $expense->expense_date,
                'icon' => $this->getCategoryIcon($expense->category)
            ]);
        }

        // Sort by date
        $recentActivities = $recentActivities->sortByDesc('date')->take(5);

        return view('dashboard.index', compact(
            'thisMonthTotal',
            'budgetLeft',
            'groupExpensesThisMonth',
            'userOwes',
            'recentActivities'
        ));
    }

    private function getCategoryIcon($category)
    {
        $icons = [
            'food' => 'fas fa-utensils',
            'pizza' => 'fas fa-pizza-slice',
            'gas' => 'fas fa-gas-pump',
            'fuel' => 'fas fa-gas-pump',
            'transport' => 'fas fa-car',
            'entertainment' => 'fas fa-film',
            'shopping' => 'fas fa-shopping-cart',
            'groceries' => 'fas fa-shopping-basket',
            'utilities' => 'fas fa-lightbulb',
            'healthcare' => 'fas fa-medkit',
            'education' => 'fas fa-graduation-cap',
            'travel' => 'fas fa-plane',
            'other' => 'fas fa-receipt'
        ];

        return $icons[strtolower($category)] ?? 'fas fa-receipt';
    }
}
