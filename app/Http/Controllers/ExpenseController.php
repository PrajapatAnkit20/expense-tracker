<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Category;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('category')
            ->orderBy('expense_date', 'desc')
            ->paginate(10);

        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'expense_date' => 'required|date',
            'payment_method' => 'required|string',
            'description' => 'nullable|string'
        ]);

        Expense::create($request->all());

        return redirect()->route('expenses.index')
            ->with('success', 'Expense added successfully!');
    }

    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $categories = Category::all();
        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'expense_date' => 'required|date',
            'payment_method' => 'required|string',
            'description' => 'nullable|string'
        ]);

        $expense->update($request->all());

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully!');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully!');
    }

    public function analytics()
    {
        $categories = Category::withSum('expenses', 'amount')->get();

        $monthlyData = Expense::selectRaw('MONTH(expense_date) as month, YEAR(expense_date) as year, SUM(amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        return view('expenses.analytics', compact('categories', 'monthlyData'));
    }
}
