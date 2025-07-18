@extends('layouts.app')

@section('content')
    <div class="analytics-container">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="page-header">
                        <h1 class="page-title">
                            <i class="fas fa-chart-line"></i>
                            Analytics Dashboard
                        </h1>
                        <p class="page-subtitle">Track your spending patterns and financial insights</p>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="analytics-card personal-card">
                        <div class="card-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="card-content">
                            <h3>₹{{ number_format($trends['personal']['current'], 2) }}</h3>
                            <p>Personal Expenses</p>
                            <span class="trend {{ $trends['personal']['change'] >= 0 ? 'trend-up' : 'trend-down' }}">
                                <i class="fas fa-arrow-{{ $trends['personal']['change'] >= 0 ? 'up' : 'down' }}"></i>
                                {{ abs(round($trends['personal']['change'], 1)) }}%
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="analytics-card group-card">
                        <div class="card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="card-content">
                            <h3>₹{{ number_format($trends['group']['current'], 2) }}</h3>
                            <p>Group Expenses</p>
                            <span class="trend {{ $trends['group']['change'] >= 0 ? 'trend-up' : 'trend-down' }}">
                                <i class="fas fa-arrow-{{ $trends['group']['change'] >= 0 ? 'up' : 'down' }}"></i>
                                {{ abs(round($trends['group']['change'], 1)) }}%
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="analytics-card total-card">
                        <div class="card-icon">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <div class="card-content">
                            <h3>₹{{ number_format($trends['personal']['current'] + $trends['group']['current'], 2) }}</h3>
                            <p>Total Spending</p>
                            <span class="trend-neutral">
                                <i class="fas fa-chart-pie"></i>
                                This Month
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="analytics-card savings-card">
                        <div class="card-icon">
                            <i class="fas fa-piggy-bank"></i>
                        </div>
                        <div class="card-content">
                            <h3>₹{{ number_format(max(0, $trends['personal']['last'] + $trends['group']['last'] - ($trends['personal']['current'] + $trends['group']['current'])), 2) }}
                            </h3>
                            <p>Savings vs Last Month</p>
                            <span class="trend-neutral">
                                <i class="fas fa-coins"></i>
                                Comparison
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Comparison Chart -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="chart-container">
                        <div class="chart-header">
                            <h3><i class="fas fa-chart-line"></i> Monthly Spending Comparison</h3>
                            <p>Personal vs Group expenses over the last 12 months</p>
                        </div>
                        <div class="chart-body">
                            <canvas id="monthlyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Breakdown -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="chart-container">
                        <div class="chart-header">
                            <h3><i class="fas fa-chart-pie"></i> Personal Expenses by Category</h3>
                            <p>Last 3 months breakdown</p>
                        </div>
                        <div class="chart-body">
                            <canvas id="personalCategoryChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-container">
                        <div class="chart-header">
                            <h3><i class="fas fa-chart-pie"></i> Group Expenses by Category</h3>
                            <p>Last 3 months breakdown</p>
                        </div>
                        <div class="chart-body">
                            <canvas id="groupCategoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Spending Trends -->
            <div class="row">
                <div class="col-md-6">
                    <div class="chart-container">
                        <div class="chart-header">
                            <h3><i class="fas fa-chart-area"></i> Personal Spending Trend</h3>
                            <p>Last 6 months overview</p>
                        </div>
                        <div class="chart-body">
                            <canvas id="personalTrendChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-container">
                        <div class="chart-header">
                            <h3><i class="fas fa-chart-area"></i> Group Spending Trend</h3>
                            <p>Last 6 months overview</p>
                        </div>
                        <div class="chart-body">
                            <canvas id="groupTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Chart.js configuration and data
        const monthlyData = @json($monthlyData);
        const personalCategories = @json($categoryBreakdown['personal']);
        const groupCategories = @json($categoryBreakdown['group']);
        const personalExpenses = @json($personalExpenses);
        const groupExpenses = @json($groupExpenses);

        // Color schemes
        const colors = {
            personal: '#6366f1',
            group: '#06b6d4',
            gradient: {
                personal: 'linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%)',
                group: 'linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)'
            }
        };

        // Chart.js default configuration
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.responsive = true;
        Chart.defaults.maintainAspectRatio = false;

        // Initialize charts when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initializeMonthlyChart();
            initializeCategoryCharts();
            initializeTrendCharts();
        });

        function initializeMonthlyChart() {
            const ctx = document.getElementById('monthlyChart').getContext('2d');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: monthlyData.map(item => {
                        const date = new Date(item.month + '-01');
                        return date.toLocaleDateString('en-US', {
                            month: 'short',
                            year: 'numeric'
                        });
                    }),
                    datasets: [{
                        label: 'Personal Expenses',
                        data: monthlyData.map(item => item.personal),
                        borderColor: colors.personal,
                        backgroundColor: colors.personal + '20',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'Group Expenses',
                        data: monthlyData.map(item => item.group),
                        borderColor: colors.group,
                        backgroundColor: colors.group + '20',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₹' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        function initializeCategoryCharts() {
            // Personal Categories Chart
            const personalCtx = document.getElementById('personalCategoryChart').getContext('2d');
            const personalColors = ['#6366f1', '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444'];

            new Chart(personalCtx, {
                type: 'doughnut',
                data: {
                    labels: personalCategories.map(item => item.category),
                    datasets: [{
                        data: personalCategories.map(item => item.total),
                        backgroundColor: personalColors,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 15
                            }
                        }
                    }
                }
            });

            // Group Categories Chart
            const groupCtx = document.getElementById('groupCategoryChart').getContext('2d');
            const groupColors = ['#06b6d4', '#0891b2', '#6366f1', '#10b981', '#f59e0b', '#ef4444'];

            new Chart(groupCtx, {
                type: 'doughnut',
                data: {
                    labels: groupCategories.map(item => item.category),
                    datasets: [{
                        data: groupCategories.map(item => item.total),
                        backgroundColor: groupColors,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 15
                            }
                        }
                    }
                }
            });
        }

        function initializeTrendCharts() {
            // Personal Trend Chart
            const personalTrendCtx = document.getElementById('personalTrendChart').getContext('2d');

            new Chart(personalTrendCtx, {
                type: 'bar',
                data: {
                    labels: personalExpenses.map(item => {
                        const date = new Date(item.month + '-01');
                        return date.toLocaleDateString('en-US', {
                            month: 'short'
                        });
                    }),
                    datasets: [{
                        label: 'Amount',
                        data: personalExpenses.map(item => item.total),
                        backgroundColor: colors.personal,
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₹' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // Group Trend Chart
            const groupTrendCtx = document.getElementById('groupTrendChart').getContext('2d');

            new Chart(groupTrendCtx, {
                type: 'bar',
                data: {
                    labels: groupExpenses.map(item => {
                        const date = new Date(item.month + '-01');
                        return date.toLocaleDateString('en-US', {
                            month: 'short'
                        });
                    }),
                    datasets: [{
                        label: 'Amount',
                        data: groupExpenses.map(item => item.total),
                        backgroundColor: colors.group,
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₹' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
@endsection

<style>
    /* Analytics Dashboard Styles */
    .analytics-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 100px 0 50px;
    }

    .page-header {
        text-align: center;
        margin-bottom: 2rem;
        color: white;
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .page-title i {
        margin-right: 15px;
        color: #ffd700;
    }

    .page-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-bottom: 0;
    }

    /* Analytics Cards */
    .analytics-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .analytics-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #6366f1, #8b5cf6);
    }

    .analytics-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    .personal-card::before {
        background: linear-gradient(90deg, #6366f1, #8b5cf6);
    }

    .group-card::before {
        background: linear-gradient(90deg, #06b6d4, #0891b2);
    }

    .total-card::before {
        background: linear-gradient(90deg, #10b981, #059669);
    }

    .savings-card::before {
        background: linear-gradient(90deg, #f59e0b, #d97706);
    }

    .analytics-card .card-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
        color: white;
    }

    .personal-card .card-icon {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
    }

    .group-card .card-icon {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
    }

    .total-card .card-icon {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .savings-card .card-icon {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .analytics-card .card-content h3 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #1f2937;
    }

    .analytics-card .card-content p {
        color: #6b7280;
        margin-bottom: 1rem;
        font-weight: 500;
    }

    .trend {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .trend-up {
        background: #dcfce7;
        color: #16a34a;
    }

    .trend-down {
        background: #fee2e2;
        color: #dc2626;
    }

    .trend-neutral {
        background: #f3f4f6;
        color: #6b7280;
    }

    /* Chart Containers */
    .chart-container {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
        transition: all 0.3s ease;
    }

    .chart-container:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    .chart-header {
        margin-bottom: 2rem;
        text-align: center;
    }

    .chart-header h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .chart-header h3 i {
        margin-right: 10px;
        color: #6366f1;
    }

    .chart-header p {
        color: #6b7280;
        margin-bottom: 0;
    }

    .chart-body {
        height: 400px;
        position: relative;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .analytics-container {
            padding: 80px 0 30px;
        }

        .page-title {
            font-size: 2rem;
        }

        .analytics-card {
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .analytics-card .card-content h3 {
            font-size: 1.5rem;
        }

        .chart-container {
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .chart-body {
            height: 300px;
        }
    }

    /* Loading Animation */
    .chart-loading {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 200px;
    }

    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f4f6;
        border-top: 4px solid #6366f1;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Custom Scrollbar */
    .analytics-container::-webkit-scrollbar {
        width: 8px;
    }

    .analytics-container::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
    }

    .analytics-container::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 4px;
    }

    .analytics-container::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    /* Animation for cards */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .analytics-card {
        animation: fadeInUp 0.6s ease-out;
    }

    .chart-container {
        animation: fadeInUp 0.6s ease-out;
    }

    /* Additional visual enhancements */
    .analytics-card::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        transform: translate(50%, -50%);
    }

    .chart-container::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 50px;
        height: 50px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        transform: translate(25%, -25%);
    }
</style>
