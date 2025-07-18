@extends('layouts.app')

@section('title', 'Dashboard - ExpenseTracker')

@section('content')
    <style>
        .page-header {
            margin-bottom: 2rem;
            color: #1f2937;
        }

        .page-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #111827;
        }

        .page-header p {
            font-size: 1rem;
            color: #6b7280;
            margin-bottom: 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .stat-card .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .stat-card .stat-title {
            font-size: 0.875rem;
            color: #6b7280;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0;
        }

        .stat-card .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
        }

        .stat-card.this-month .stat-value {
            color: #111827;
        }

        .stat-card.this-month .stat-icon {
            background: #ef4444;
        }

        .stat-card.budget-left .stat-value {
            color: {{ $budgetLeft >= 0 ? '#059669' : '#dc2626' }};
        }

        .stat-card.budget-left .stat-icon {
            background: {{ $budgetLeft >= 0 ? '#10b981' : '#ef4444' }};
        }

        .stat-card.group-expenses .stat-value {
            color: #2563eb;
        }

        .stat-card.group-expenses .stat-icon {
            background: #3b82f6;
        }

        .stat-card.you-owe .stat-value {
            color: #dc2626;
        }

        .stat-card.you-owe .stat-icon {
            background: #f59e0b;
        }

        .activity-section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #e5e7eb;
            margin-bottom: 2rem;
        }

        .activity-section h2 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 1.5rem;
        }

        .activity-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .activity-icon.personal {
            background: #10b981;
            color: white;
        }

        .activity-icon.group {
            background: #3b82f6;
            color: white;
        }

        .activity-details {
            flex-grow: 1;
        }

        .activity-title {
            font-size: 1rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.25rem;
        }

        .activity-subtitle {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 0;
        }

        .activity-amount {
            text-align: right;
            flex-shrink: 0;
        }

        .activity-price {
            font-size: 1rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.25rem;
        }

        .activity-share {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 0;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #d1d5db;
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.75rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .stat-card {
                padding: 1.25rem;
            }

            .activity-section {
                padding: 1.25rem;
            }

            .activity-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }

            .activity-amount {
                text-align: left;
                width: 100%;
            }
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <h1>Dashboard Overview</h1>
        <p>Track your personal and group expenses</p>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card this-month">
            <div class="stat-header">
                <div>
                    <div class="stat-title">This Month</div>
                    <div class="stat-value">₹{{ number_format($thisMonthTotal, 2) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>

        <div class="stat-card budget-left">
            <div class="stat-header">
                <div>
                    <div class="stat-title">Budget Left</div>
                    <div class="stat-value">₹{{ number_format($budgetLeft, 2) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-{{ $budgetLeft >= 0 ? 'heart' : 'exclamation-triangle' }}"></i>
                </div>
            </div>
        </div>

        <div class="stat-card group-expenses">
            <div class="stat-header">
                <div>
                    <div class="stat-title">Group Expenses</div>
                    <div class="stat-value">₹{{ number_format($groupExpensesThisMonth, 2) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <div class="stat-card you-owe">
            <div class="stat-header">
                <div>
                    <div class="stat-title">You Owe</div>
                    <div class="stat-value">₹{{ number_format($userOwes, 2) }}</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="activity-section">
        <h2>Recent Activity</h2>

        @if ($recentActivities->count() > 0)
            @foreach ($recentActivities as $activity)
                <div class="activity-item">
                    <div class="activity-icon {{ $activity['type'] }}">
                        <i class="{{ $activity['icon'] }}"></i>
                    </div>
                    <div class="activity-details">
                        <div class="activity-title">{{ $activity['title'] }}</div>
                        <div class="activity-subtitle">{{ $activity['subtitle'] }}</div>
                    </div>
                    <div class="activity-amount">
                        <div class="activity-price">₹{{ number_format($activity['amount'], 2) }}</div>
                        @if ($activity['type'] === 'group')
                            <div class="activity-share">Your share: ₹{{ number_format($activity['user_share'], 2) }}</div>
                        @else
                            <div class="activity-share">{{ ucfirst($activity['category']) }}</div>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <i class="fas fa-receipt"></i>
                <p>No recent activity found</p>
                <p>Start by adding your first expense!</p>
            </div>
        @endif
    </div>
@endsection
