<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'description',
        'category',
        'expense_date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date'
    ];

    /**
     * Get the user that owns the expense.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category icon based on category name
     */
    public function getCategoryIconAttribute(): string
    {
        $icons = [
            'Food & Dining' => 'fas fa-utensils',
            'Transportation' => 'fas fa-car',
            'Shopping' => 'fas fa-shopping-cart',
            'Entertainment' => 'fas fa-film',
            'Health & Medical' => 'fas fa-medkit',
            'Bills & Utilities' => 'fas fa-file-invoice-dollar',
            'Education' => 'fas fa-graduation-cap',
            'Travel' => 'fas fa-plane',
            'Other' => 'fas fa-question-circle'
        ];

        return $icons[$this->category] ?? 'fas fa-question-circle';
    }

    /**
     * Get the category color based on category name
     */
    public function getCategoryColorAttribute(): string
    {
        $colors = [
            'Food & Dining' => '#ef4444',
            'Transportation' => '#10b981',
            'Shopping' => '#3b82f6',
            'Entertainment' => '#8b5cf6',
            'Health & Medical' => '#f59e0b',
            'Bills & Utilities' => '#06b6d4',
            'Education' => '#84cc16',
            'Travel' => '#ec4899',
            'Other' => '#6b7280'
        ];

        return $colors[$this->category] ?? '#6b7280';
    }

    /**
     * Scope to get expenses for current month
     */
    public function scopeCurrentMonth($query)
    {
        return $query->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year);
    }

    /**
     * Scope to get expenses for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get expenses without user filtering
     */
    public function scopeWithoutUser($query)
    {
        return $query->whereNull('user_id');
    }
}
