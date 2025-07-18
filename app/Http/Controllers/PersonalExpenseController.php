<?php

namespace App\Http\Controllers;

use App\Models\PersonalExpense;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PersonalExpenseController extends Controller
{
    /**
     * Display the personal expenses page
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $category = $request->get('category', 'all');
        $month = $request->get('month', now()->format('Y-m'));

        // Base query (without user filtering)
        $query = PersonalExpense::orderBy('expense_date', 'desc')
            ->orderBy('created_at', 'desc');

        // Apply category filter
        if ($category !== 'all') {
            $query->where('category', $category);
        }

        // Apply month filter
        if ($month) {
            $date = Carbon::createFromFormat('Y-m', $month);
            $query->whereMonth('expense_date', $date->month)
                ->whereYear('expense_date', $date->year);
        }

        // Get recent expenses
        $expenses = $query->take(10)->get();

        // Get budget information
        $monthlyBudget = 2000.00; // You can make this dynamic later
        $currentMonthExpenses = PersonalExpense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        $budgetRemaining = $monthlyBudget - $currentMonthExpenses;
        $budgetUsedPercentage = ($currentMonthExpenses / $monthlyBudget) * 100;

        // Get categories for filter
        $categories = [
            'Food & Dining',
            'Transportation',
            'Shopping',
            'Entertainment',
            'Health & Medical',
            'Bills & Utilities',
            'Education',
            'Travel',
            'Other'
        ];

        return view('personal.index', compact(
            'expenses',
            'monthlyBudget',
            'currentMonthExpenses',
            'budgetRemaining',
            'budgetUsedPercentage',
            'categories',
            'category',
            'month'
        ));
    }

    /**
     * Store a new personal expense
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'category' => 'required|string|in:Food & Dining,Transportation,Shopping,Entertainment,Health & Medical,Bills & Utilities,Education,Travel,Other',
            'expense_date' => 'required|date'
        ]);

        PersonalExpense::create([
            'user_id' => 1, // Use a default user ID or make it nullable
            'amount' => $request->amount,
            'description' => $request->description,
            'category' => $request->category,
            'expense_date' => $request->expense_date
        ]);

        return redirect()->route('personal.index')
            ->with('success', 'Expense added successfully!');
    }

    /**
     * Delete a personal expense
     */
    public function destroy(PersonalExpense $expense)
    {
        $expense->delete();

        return redirect()->route('personal.index')
            ->with('success', 'Expense deleted successfully!');
    }

    /**
     * Get expenses data for AJAX requests
     */
    public function getExpenses(Request $request)
    {
        $category = $request->get('category', 'all');

        $query = PersonalExpense::orderBy('expense_date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($category !== 'all') {
            $query->where('category', $category);
        }

        $expenses = $query->take(10)->get();

        return response()->json([
            'expenses' => $expenses->map(function ($expense) {
                return [
                    'id' => $expense->id,
                    'amount' => $expense->amount,
                    'description' => $expense->description,
                    'category' => $expense->category,
                    'expense_date' => $expense->expense_date->format('M d, Y'),
                    'category_icon' => $expense->category_icon,
                    'category_color' => $expense->category_color,
                    'formatted_amount' => '₹' . number_format($expense->amount, 2),
                    'time_ago' => $expense->created_at->diffForHumans()
                ];
            })
        ]);
    }
}
