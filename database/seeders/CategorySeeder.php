<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Food & Dining', 'color' => '#FF6B6B', 'icon' => '🍽️'],
            ['name' => 'Transportation', 'color' => '#4ECDC4', 'icon' => '🚗'],
            ['name' => 'Shopping', 'color' => '#45B7D1', 'icon' => '🛍️'],
            ['name' => 'Entertainment', 'color' => '#96CEB4', 'icon' => '🎬'],
            ['name' => 'Bills & Utilities', 'color' => '#FFEAA7', 'icon' => '💡'],
            ['name' => 'Healthcare', 'color' => '#DDA0DD', 'icon' => '🏥'],
            ['name' => 'Education', 'color' => '#98D8C8', 'icon' => '📚'],
            ['name' => 'Travel', 'color' => '#F7DC6F', 'icon' => '✈️'],
            ['name' => 'Groceries', 'color' => '#82E0AA', 'icon' => '🛒'],
            ['name' => 'Other', 'color' => '#BDC3C7', 'icon' => '📦'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
