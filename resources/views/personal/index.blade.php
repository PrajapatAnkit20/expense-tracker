@extends('layouts.app')

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Expenses - ExpenseTracker</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            padding-top: 70px;
        
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .page-header {
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .page-header h1 {
            color: black;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: rgba(49, 47, 47, 0.8);
            font-size: 1.1rem;
            margin-bottom: 0;
        }

        .main-content {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .expense-form {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .expense-form h2 {
            color: #1f2937;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3);
        }

        .budget-section {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .budget-section h3 {
            color: #1f2937;
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .budget-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding: 0.5rem 0;
        }

        .budget-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .budget-label {
            color: #6b7280;
            font-weight: 500;
        }

        .budget-value {
            font-weight: 600;
        }

        .budget-value.total {
            color: #1f2937;
        }

        .budget-value.spent {
            color: #ef4444;
        }

        .budget-value.remaining {
            color: #10b981;
        }

        .progress {
            height: 8px;
            border-radius: 4px;
            margin-top: 1rem;
            background: #f3f4f6;
        }

        .progress-bar {
            border-radius: 4px;
            background: linear-gradient(135deg, #ef4444 0%, #f59e0b 100%);
        }

        .expenses-section {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .expenses-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .expenses-header h2 {
            color: #1f2937;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0;
        }

        .filter-select {
            padding: 0.5rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.875rem;
            min-width: 150px;
        }

        .expense-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.3s ease;
        }

        .expense-item:hover {
            background: #f9fafb;
            margin: 0 -1rem;
            padding-left: 1rem;
            padding-right: 1rem;
            border-radius: 10px;
        }

        .expense-item:last-child {
            border-bottom: none;
        }

        .expense-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .expense-details {
            flex-grow: 1;
        }

        .expense-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }

        .expense-meta {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 0;
        }

        .expense-amount {
            text-align: right;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .expense-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1f2937;
        }

        .delete-btn {
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .delete-btn:hover {
            background: #dc2626;
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
        }

        .no-expenses {
            text-align: center;
            color: #6b7280;
            padding: 2rem;
        }

        .no-expenses i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #d1d5db;
        }

        @media (max-width: 768px) {
            .main-content {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .page-header {
                padding: 1.5rem;
            }

            .page-header h1 {
                font-size: 2rem;
            }

            .expense-form,
            .budget-section,
            .expenses-section {
                padding: 1.5rem;
            }

            .expenses-header {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }

            .expense-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }

            .expense-amount {
                text-align: left;
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1>Personal Expenses</h1>
            <p>Manage your individual expenses and budgets</p>
        </div>

        <!-- Success Alert -->
        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Main Content -->
        <div class="main-content">
            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Add New Expense Form -->
                <div class="expense-form">
                    <h2>Add New Expense</h2>
                    <form action="{{ route('personal.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="number" id="amount" name="amount" class="form-control" placeholder="0.00"
                                step="0.01" required value="{{ old('amount') }}">
                            @error('amount')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <input type="text" id="description" name="description" class="form-control"
                                placeholder="What did you spend on?" required value="{{ old('description') }}">
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="category">Category</label>
                            <select id="category" name="category" class="form-control" required>
                                <option value="">Select category</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="expense_date">Date</label>
                            <input type="date" id="expense_date" name="expense_date" class="form-control" required
                                value="{{ old('expense_date', date('Y-m-d')) }}">
                            @error('expense_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add Expense
                        </button>
                    </form>
                </div>

                <!-- Monthly Budget -->
                <div class="budget-section">
                    <h3>Monthly Budget</h3>
                    <div class="budget-item">
                        <span class="budget-label">Total Budget</span>
                        <span class="budget-value total">₹{{ number_format($monthlyBudget, 2) }}</span>
                    </div>
                    <div class="budget-item">
                        <span class="budget-label">Spent</span>
                        <span class="budget-value spent">₹{{ number_format($currentMonthExpenses, 2) }}</span>
                    </div>
                    <div class="budget-item">
                        <span class="budget-label">Remaining</span>
                        <span class="budget-value remaining">₹{{ number_format($budgetRemaining, 2) }}</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" style="width: {{ min($budgetUsedPercentage, 100) }}%"></div>
                    </div>
                    <small class="text-muted mt-1">{{ number_format($budgetUsedPercentage, 1) }}% of budget
                        used</small>
                </div>
            </div>

            <!-- Recent Expenses -->
            <div class="expenses-section">
                <div class="expenses-header">
                    <h2>Recent Expenses</h2>
                    <select class="filter-select" id="categoryFilter">
                        <option value="all">All Categories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="expensesList">
                    @forelse($expenses as $expense)
                        <div class="expense-item">
                            <div class="expense-icon" style="background-color: {{ $expense->category_color }}">
                                <i class="{{ $expense->category_icon }}"></i>
                            </div>
                            <div class="expense-details">
                                <div class="expense-title">{{ $expense->description }}</div>
                                <div class="expense-meta">
                                    {{ $expense->category }} • {{ $expense->expense_date->format('M d, Y') }}
                                </div>
                            </div>
                            <div class="expense-amount">
                                <div class="expense-price">₹{{ number_format($expense->amount, 2) }}</div>
                                <form action="{{ route('personal.destroy', $expense) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn"
                                        onclick="return confirm('Are you sure you want to delete this expense?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="no-expenses">
                            <i class="fas fa-receipt"></i>
                            <p>No expenses found. Start by adding your first expense!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // AJAX filter functionality
        document.getElementById('categoryFilter').addEventListener('change', function() {
            const category = this.value;

            fetch(`/api/personal/expenses?category=${category}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const expensesList = document.getElementById('expensesList');

                    if (data.expenses.length === 0) {
                        expensesList.innerHTML = `
                        <div class="no-expenses">
                            <i class="fas fa-receipt"></i>
                            <p>No expenses found for this category.</p>
                        </div>
                    `;
                    } else {
                        expensesList.innerHTML = data.expenses.map(expense => `
                        <div class="expense-item">
                            <div class="expense-icon" style="background-color: ${expense.category_color}">
                                <i class="${expense.category_icon}"></i>
                            </div>
                            <div class="expense-details">
                                <div class="expense-title">${expense.description}</div>
                                <div class="expense-meta">
                                    ${expense.category} • ${expense.expense_date}
                                </div>
                            </div>
                            <div class="expense-amount">
                                <div class="expense-price">${expense.formatted_amount}</div>
                                <form action="/personal/${expense.id}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this expense?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    `).join('');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    </script>
</body>

</html>
