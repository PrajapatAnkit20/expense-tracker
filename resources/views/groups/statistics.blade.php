@extends('layouts.app')

@section('title', 'Statistics - ' . $group->name)

@section('content')
    <div class="container-fluid">
        <!-- Group Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center">
                                    @if ($group->image)
                                        <img src="{{ asset('storage/' . $group->image) }}" alt="{{ $group->name }}"
                                            class="rounded-circle me-3" width="60" height="60">
                                    @else
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 60px; height: 60px;">
                                            <i class="fas fa-users text-white fa-lg"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h2 class="mb-1">{{ $group->name }} - Statistics</h2>
                                        <p class="text-muted mb-0">{{ $group->description }}</p>
                                        <small class="text-muted">Created on
                                            {{ $group->created_at->format('M d, Y') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('groups.show', $group) }}" class="btn btn-secondary me-2">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Back to Group
                                </a>
                                <a href="{{ route('groups.export', $group) }}" class="btn btn-primary">
                                    <i class="fas fa-download me-2"></i>
                                    Export Data
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <i class="fas fa-rupee-sign fa-3x text-primary mb-3"></i>
                        <h3 class="text-primary">₹{{ number_format($totalExpenses, 2) }}</h3>
                        <h6 class="text-muted">Total Expenses</h6>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-3x text-success mb-3"></i>
                        <h3 class="text-success">{{ $memberCount }}</h3>
                        <h6 class="text-muted">Active Members</h6>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <i class="fas fa-receipt fa-3x text-info mb-3"></i>
                        <h3 class="text-info">{{ $totalTransactions }}</h3>
                        <h6 class="text-muted">Total Transactions</h6>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stat-card">
                    <div class="card-body text-center">
                        <i class="fas fa-calculator fa-3x text-warning mb-3"></i>
                        <h3 class="text-warning">₹{{ number_format($averageExpense, 2) }}</h3>
                        <h6 class="text-muted">Average Expense</h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Expense Breakdown by Category -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>
                            Expense Breakdown by Category
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($categoryBreakdown->isEmpty())
                            <div class="text-center py-4">
                                <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No expenses recorded yet</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th class="text-end">Amount</th>
                                            <th class="text-end">Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($categoryBreakdown as $category)
                                            <tr>
                                                <td>
                                                    <span class="category-badge"
                                                        style="background-color: {{ $category->color ?? '#007bff' }};">
                                                        <i class="{{ $category->icon ?? 'fas fa-circle' }}"></i>
                                                    </span>
                                                    {{ $category->name }}
                                                </td>
                                                <td class="text-end">₹{{ number_format($category->total, 2) }}</td>
                                                <td class="text-end">{{ number_format($category->percentage, 1) }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Member Spending Analysis -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user-chart me-2"></i>
                            Member Spending Analysis
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($memberSpending->isEmpty())
                            <div class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No member spending data available</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Member</th>
                                            <th class="text-end">Paid</th>
                                            <th class="text-end">Owes</th>
                                            <th class="text-end">Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($memberSpending as $member)
                                            <tr>
                                                <td>
                                                    <img src="{{ $member->avatar ?? '/default-avatar.png' }}"
                                                        alt="{{ $member->name }}" class="rounded-circle me-2"
                                                        width="25" height="25">
                                                    {{ $member->name }}
                                                </td>
                                                <td class="text-end">₹{{ number_format($member->total_paid, 2) }}</td>
                                                <td class="text-end">₹{{ number_format($member->total_owes, 2) }}</td>
                                                <td class="text-end">
                                                    <span
                                                        class="badge 
                                                        @if ($member->balance > 0) bg-success
                                                        @elseif($member->balance < 0) bg-danger
                                                        @else bg-secondary @endif
                                                    ">
                                                        ₹{{ number_format($member->balance, 2) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Spending Trend -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            Monthly Spending Trend
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($monthlyTrend->isEmpty())
                            <div class="text-center py-4">
                                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No monthly trend data available</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th class="text-end">Total Expenses</th>
                                            <th class="text-end">Number of Transactions</th>
                                            <th class="text-end">Average per Transaction</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($monthlyTrend as $month)
                                            <tr>
                                                <td>{{ $month->month_name }}</td>
                                                <td class="text-end">₹{{ number_format($month->total_amount, 2) }}</td>
                                                <td class="text-end">{{ $month->transaction_count }}</td>
                                                <td class="text-end">₹{{ number_format($month->average_amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods & Recent Activity -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>
                            Payment Methods
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($paymentMethods->isEmpty())
                            <div class="text-center py-4">
                                <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No payment method data available</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Method</th>
                                            <th class="text-end">Amount</th>
                                            <th class="text-end">Usage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($paymentMethods as $method)
                                            <tr>
                                                <td>
                                                    <i class="fas fa-{{ $method->icon ?? 'circle' }} me-2"></i>
                                                    {{ $method->method ?? 'Unknown' }}
                                                </td>
                                                <td class="text-end">₹{{ number_format($method->total_amount, 2) }}</td>
                                                <td class="text-end">{{ $method->usage_count }} times</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-clock me-2"></i>
                            Recent Activity
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($recentActivity->isEmpty())
                            <div class="text-center py-4">
                                <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No recent activity</p>
                            </div>
                        @else
                            <div class="timeline">
                                @foreach ($recentActivity as $activity)
                                    <div class="timeline-item">
                                        <div class="timeline-marker">
                                            <i class="fas fa-{{ $activity->type == 'expense' ? 'receipt' : 'user' }}"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">{{ $activity->title }}</h6>
                                            <p class="text-muted mb-1">{{ $activity->description }}</p>
                                            <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Expense Summary -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-file-alt me-2"></i>
                            Expense Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="text-center summary-item">
                                    <i class="fas fa-arrow-up text-danger mb-2"></i>
                                    <h6 class="text-muted">Highest Expense</h6>
                                    <h4 class="text-danger">₹{{ number_format($highestExpense, 2) }}</h4>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="text-center summary-item">
                                    <i class="fas fa-arrow-down text-success mb-2"></i>
                                    <h6 class="text-muted">Lowest Expense</h6>
                                    <h4 class="text-success">₹{{ number_format($lowestExpense, 2) }}</h4>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="text-center summary-item">
                                    <i class="fas fa-star text-primary mb-2"></i>
                                    <h6 class="text-muted">Most Active Month</h6>
                                    <h4 class="text-primary">{{ $mostActiveMonth ?? 'N/A' }}</h4>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="text-center summary-item">
                                    <i class="fas fa-calendar-alt text-info mb-2"></i>
                                    <h6 class="text-muted">Days Since Last Expense</h6>
                                    <h4 class="text-info">{{ $daysSinceLastExpense ?? 'N/A' }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        /* Statistics Cards */
        .stat-card {
            transition: all 0.3s ease-in-out;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-card .card-body {
            padding: 2rem 1rem;
        }

        .stat-card i {
            opacity: 0.8;
        }

        .stat-card h3 {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        /* Category Badge */
        .category-badge {
            display: inline-block;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            text-align: center;
            line-height: 25px;
            color: white;
            font-size: 12px;
            margin-right: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Timeline Styles */
        .timeline {
            position: relative;
            padding-left: 30px;
            max-height: 400px;
            overflow-y: auto;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, #007bff, #e9ecef);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-marker {
            position: absolute;
            left: -23px;
            top: 0;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
            z-index: 1;
        }

        .timeline-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 3px solid #007bff;
            margin-left: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .timeline-content:hover {
            background: #e9ecef;
            transform: translateX(5px);
        }

        .timeline-content h6 {
            color: #495057;
            font-weight: 600;
        }

        .timeline-content p {
            font-size: 0.9rem;
            line-height: 1.4;
        }

        /* Card Enhancements */
        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
        }

        .card-header h5 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 0;
        }

        .card-header i {
            color: #007bff;
        }

        /* Summary Items */
        .summary-item {
            padding: 1rem;
            border-radius: 8px;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            transition: all 0.3s ease;
        }

        .summary-item:hover {
            background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%);
            transform: translateY(-2px);
        }

        .summary-item i {
            font-size: 1.5rem;
        }

        .summary-item h4 {
            font-weight: bold;
            margin-bottom: 0;
        }

        .summary-item h6 {
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Table Enhancements */
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            color: #495057;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
        }

        .table td {
            vertical-align: middle;
            padding: 0.75rem;
        }

        /* Badge Enhancements */
        .badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
        }

        /* Empty State Improvements */
        .text-center.py-4 {
            padding: 3rem 1rem !important;
        }

        .text-center.py-4 i {
            opacity: 0.5;
        }

        .text-center.py-4 p {
            font-size: 1.1rem;
            margin-top: 1rem;
        }

        /* Responsive Improvements */
        @media (max-width: 768px) {
            .stat-card .card-body {
                padding: 1.5rem 1rem;
            }

            .timeline {
                padding-left: 25px;
            }

            .timeline-marker {
                left: -20px;
                width: 25px;
                height: 25px;
                font-size: 10px;
            }

            .timeline-content {
                margin-left: 10px;
                padding: 12px;
            }

            .summary-item {
                margin-bottom: 1rem;
            }
        }

        @media (max-width: 576px) {
            .container-fluid {
                padding: 0.5rem;
            }

            .card-body {
                padding: 1rem;
            }

            .btn {
                font-size: 0.875rem;
                padding: 0.5rem 1rem;
            }

            .stat-card h3 {
                font-size: 1.5rem;
            }
        }

        /* Animation for loading states */
        .loading-shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }

        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }
    </style>
@endsection
