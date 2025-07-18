<?php
// database/seeders/DashboardSampleDataSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PersonalExpense;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupExpense;
use App\Models\GroupExpenseSplit;
use App\Models\Category;
use Carbon\Carbon;

class DashboardSampleDataSeeder extends Seeder
{
    public function run()
    {
        // Create categories
        $categories = [
            ['name' => 'Food', 'color' => '#ef4444', 'icon' => 'fas fa-utensils'],
            ['name' => 'Transport', 'color' => '#10b981', 'icon' => 'fas fa-car'],
            ['name' => 'Entertainment', 'color' => '#3b82f6', 'icon' => 'fas fa-film'],
            ['name' => 'Shopping', 'color' => '#f59e0b', 'icon' => 'fas fa-shopping-cart'],
            ['name' => 'Utilities', 'color' => '#8b5cf6', 'icon' => 'fas fa-lightbulb'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Assuming you have a user with ID 1, or create one for testing
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Create personal expenses
        $personalExpenses = [
            [
                'user_id' => $user->id,
                'amount' => 2500.00,
                'description' => 'Grocery Shopping',
                'category' => 'food',
                'expense_date' => Carbon::now()->subDays(2),
            ],
            [
                'user_id' => $user->id,
                'amount' => 1200.00,
                'description' => 'Gas Station',
                'category' => 'fuel',
                'expense_date' => Carbon::now()->subDays(1),
            ],
            [
                'user_id' => $user->id,
                'amount' => 800.00,
                'description' => 'Movie Tickets',
                'category' => 'entertainment',
                'expense_date' => Carbon::now()->subDays(3),
            ],
            [
                'user_id' => $user->id,
                'amount' => 3500.00,
                'description' => 'Electricity Bill',
                'category' => 'utilities',
                'expense_date' => Carbon::now()->subDays(5),
            ],
        ];

        foreach ($personalExpenses as $expense) {
            PersonalExpense::create($expense);
        }

        // Create a group
        $group = Group::create([
            'name' => 'Weekend Friends',
            'description' => 'Friends group for weekend activities',
            'created_by' => $user->id,
            'is_active' => true,
        ]);

        // Add members to the group
        $member1 = GroupMember::create([
            'group_id' => $group->id,
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'admin',
            'status' => 'active',
            'joined_at' => Carbon::now()->subDays(30),
        ]);

        $member2 = GroupMember::create([
            'group_id' => $group->id,
            'user_id' => null,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'member',
            'status' => 'active',
            'joined_at' => Carbon::now()->subDays(25),
        ]);

        $member3 = GroupMember::create([
            'group_id' => $group->id,
            'user_id' => null,
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'role' => 'member',
            'status' => 'active',
            'joined_at' => Carbon::now()->subDays(20),
        ]);

        // Create group expenses
        $groupExpense1 = GroupExpense::create([
            'group_id' => $group->id,
            'paid_by' => $member1->id,
            'title' => 'Pizza Night',
            'description' => 'Dinner at Pizza Place',
            'amount' => 1800.00,
            'category' => 'food',
            'expense_date' => Carbon::now()->subDays(1),
            'payment_method' => 'cash',
        ]);

        $groupExpense2 = GroupExpense::create([
            'group_id' => $group->id,
            'paid_by' => $member2->id,
            'title' => 'Movie Night',
            'description' => 'Movie tickets and snacks',
            'amount' => 1200.00,
            'category' => 'entertainment',
            'expense_date' => Carbon::now()->subDays(3),
            'payment_method' => 'card',
        ]);

        // Create expense splits for Pizza Night (split equally among 3 members)
        $splitAmount1 = $groupExpense1->amount / 3;
        GroupExpenseSplit::create([
            'group_expense_id' => $groupExpense1->id,
            'member_id' => $member1->id,
            'amount' => $splitAmount1,
            'split_type' => 'equal',
            'is_paid' => true, // The person who paid
            'paid_at' => Carbon::now()->subDays(1),
        ]);

        GroupExpenseSplit::create([
            'group_expense_id' => $groupExpense1->id,
            'member_id' => $member2->id,
            'amount' => $splitAmount1,
            'split_type' => 'equal',
            'is_paid' => false, // Owes money
        ]);

        GroupExpenseSplit::create([
            'group_expense_id' => $groupExpense1->id,
            'member_id' => $member3->id,
            'amount' => $splitAmount1,
            'split_type' => 'equal',
            'is_paid' => false, // Owes money
        ]);

        // Create expense splits for Movie Night
        $splitAmount2 = $groupExpense2->amount / 3;
        GroupExpenseSplit::create([
            'group_expense_id' => $groupExpense2->id,
            'member_id' => $member1->id,
            'amount' => $splitAmount2,
            'split_type' => 'equal',
            'is_paid' => false, // User owes money
        ]);

        GroupExpenseSplit::create([
            'group_expense_id' => $groupExpense2->id,
            'member_id' => $member2->id,
            'amount' => $splitAmount2,
            'split_type' => 'equal',
            'is_paid' => true, // The person who paid
            'paid_at' => Carbon::now()->subDays(3),
        ]);

        GroupExpenseSplit::create([
            'group_expense_id' => $groupExpense2->id,
            'member_id' => $member3->id,
            'amount' => $splitAmount2,
            'split_type' => 'equal',
            'is_paid' => true,
            'paid_at' => Carbon::now()->subDays(2),
        ]);
    }
}
