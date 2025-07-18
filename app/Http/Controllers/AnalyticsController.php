<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Since there's no authentication, we'll use a default user ID or get all data
        // Option 1: Use a default user ID (e.g., 1)
        $userId = 1;

        // Option 2: Or you could get data for all users (remove user_id conditions)
        // In this case, you'd need to modify all the private methods

        // Get personal expenses data
        $personalExpenses = $this->getPersonalExpensesData($userId);

        // Get group expenses data
        $groupExpenses = $this->getGroupExpensesData($userId);

        // Get monthly comparison data
        $monthlyData = $this->getMonthlyComparisonData($userId);

        // Get category breakdown
        $categoryBreakdown = $this->getCategoryBreakdown($userId);

        // Get recent trends
        $trends = $this->getTrendsData($userId);

        return view('analytics.index', compact(
            'personalExpenses',
            'groupExpenses',
            'monthlyData',
            'categoryBreakdown',
            'trends'
        ));
    }

    private function getPersonalExpensesData($userId)
    {
        return DB::table('personal_expenses')
            ->where('user_id', $userId)
            ->select(
                DB::raw('DATE_FORMAT(expense_date, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->where('expense_date', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getGroupExpensesData($userId)
    {
        return DB::table('group_expense_splits')
            ->join('group_expenses', 'group_expense_splits.group_expense_id', '=', 'group_expenses.id')
            ->join('group_members', 'group_expense_splits.member_id', '=', 'group_members.id')
            ->where('group_members.user_id', $userId)
            ->select(
                DB::raw('DATE_FORMAT(group_expenses.expense_date, "%Y-%m") as month'),
                DB::raw('SUM(group_expense_splits.amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->where('group_expenses.expense_date', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getMonthlyComparisonData($userId)
    {
        $personalMonthly = DB::table('personal_expenses')
            ->where('user_id', $userId)
            ->select(
                DB::raw('DATE_FORMAT(expense_date, "%Y-%m") as month'),
                DB::raw('SUM(amount) as personal_total')
            )
            ->where('expense_date', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $groupMonthly = DB::table('group_expense_splits')
            ->join('group_expenses', 'group_expense_splits.group_expense_id', '=', 'group_expenses.id')
            ->join('group_members', 'group_expense_splits.member_id', '=', 'group_members.id')
            ->where('group_members.user_id', $userId)
            ->select(
                DB::raw('DATE_FORMAT(group_expenses.expense_date, "%Y-%m") as month'),
                DB::raw('SUM(group_expense_splits.amount) as group_total')
            )
            ->where('group_expenses.expense_date', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        // Merge the data
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i)->format('Y-m');
            $months[] = [
                'month' => $month,
                'personal' => $personalMonthly[$month]->personal_total ?? 0,
                'group' => $groupMonthly[$month]->group_total ?? 0
            ];
        }

        return $months;
    }

    private function getCategoryBreakdown($userId)
    {
        $personal = DB::table('personal_expenses')
            ->where('user_id', $userId)
            ->select('category', DB::raw('SUM(amount) as total'))
            ->where('expense_date', '>=', Carbon::now()->subMonths(3))
            ->groupBy('category')
            ->get();

        $group = DB::table('group_expense_splits')
            ->join('group_expenses', 'group_expense_splits.group_expense_id', '=', 'group_expenses.id')
            ->join('group_members', 'group_expense_splits.member_id', '=', 'group_members.id')
            ->where('group_members.user_id', $userId)
            ->select('group_expenses.category', DB::raw('SUM(group_expense_splits.amount) as total'))
            ->where('group_expenses.expense_date', '>=', Carbon::now()->subMonths(3))
            ->groupBy('group_expenses.category')
            ->get();

        return [
            'personal' => $personal,
            'group' => $group
        ];
    }

    private function getTrendsData($userId)
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $lastMonth = Carbon::now()->subMonth()->format('Y-m');

        $currentPersonal = DB::table('personal_expenses')
            ->where('user_id', $userId)
            ->whereRaw('DATE_FORMAT(expense_date, "%Y-%m") = ?', [$currentMonth])
            ->sum('amount');

        $lastPersonal = DB::table('personal_expenses')
            ->where('user_id', $userId)
            ->whereRaw('DATE_FORMAT(expense_date, "%Y-%m") = ?', [$lastMonth])
            ->sum('amount');

        $currentGroup = DB::table('group_expense_splits')
            ->join('group_expenses', 'group_expense_splits.group_expense_id', '=', 'group_expenses.id')
            ->join('group_members', 'group_expense_splits.member_id', '=', 'group_members.id')
            ->where('group_members.user_id', $userId)
            ->whereRaw('DATE_FORMAT(group_expenses.expense_date, "%Y-%m") = ?', [$currentMonth])
            ->sum('group_expense_splits.amount');

        $lastGroup = DB::table('group_expense_splits')
            ->join('group_expenses', 'group_expense_splits.group_expense_id', '=', 'group_expenses.id')
            ->join('group_members', 'group_expense_splits.member_id', '=', 'group_members.id')
            ->where('group_members.user_id', $userId)
            ->whereRaw('DATE_FORMAT(group_expenses.expense_date, "%Y-%m") = ?', [$lastMonth])
            ->sum('group_expense_splits.amount');

        return [
            'personal' => [
                'current' => $currentPersonal,
                'last' => $lastPersonal,
                'change' => $lastPersonal > 0 ? (($currentPersonal - $lastPersonal) / $lastPersonal) * 100 : 0
            ],
            'group' => [
                'current' => $currentGroup,
                'last' => $lastGroup,
                'change' => $lastGroup > 0 ? (($currentGroup - $lastGroup) / $lastGroup) * 100 : 0
            ]
        ];
    }
}
