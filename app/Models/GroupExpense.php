<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'paid_by',
        'title',
        'description',
        'amount',
        'category',
        'expense_date',
        'payment_method',
        'receipt_image',
        'is_settlement'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'is_settlement' => 'boolean'
    ];

    /**
     * Get the group this expense belongs to
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the member who paid for this expense
     */
    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(GroupMember::class, 'paid_by');
    }

    public function paidByMember()
    {
        return $this->belongsTo(GroupMember::class, 'paid_by');
    }

    /**
     * Get all splits for this expense
     */
    public function splits(): HasMany
    {
        return $this->hasMany(GroupExpenseSplit::class);
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
            'Accommodation' => 'fas fa-bed',
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
            'Accommodation' => '#f97316',
            'Other' => '#6b7280'
        ];

        return $colors[$this->category] ?? '#6b7280';
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₹' . number_format($this->amount, 2);
    }

    /**
     * Get split amount per member (for equal split)
     */
    public function getSplitAmountAttribute(): float
    {
        $memberCount = $this->splits()->count();
        return $memberCount > 0 ? $this->amount / $memberCount : 0;
    }

    /**
     * Check if expense is fully settled
     */
    public function isSettled(): bool
    {
        return $this->splits()->where('is_paid', false)->count() === 0;
    }

    /**
     * Get unsettled amount
     */
    public function getUnsettledAmountAttribute(): float
    {
        return $this->splits()->where('is_paid', false)->sum('amount');
    }

    /**
     * Get involved members
     */
    public function getInvolvedMembersAttribute()
    {
        return $this->splits()->with('member')->get()->pluck('member');
    }

    /**
     * Split expense equally among members
     */
    public function splitEqually(array $memberIds): void
    {
        $splitAmount = $this->amount / count($memberIds);

        foreach ($memberIds as $memberId) {
            $this->splits()->create([
                'member_id' => $memberId,
                'amount' => $splitAmount,
                'split_type' => 'equal'
            ]);
        }
    }

    /**
     * Split expense by exact amounts
     */
    public function splitExactly(array $splits): void
    {
        foreach ($splits as $split) {
            $this->splits()->create([
                'member_id' => $split['member_id'],
                'amount' => $split['amount'],
                'split_type' => 'exact'
            ]);
        }
    }

    /**
     * Split expense by percentages
     */
    public function splitByPercentage(array $splits): void
    {
        foreach ($splits as $split) {
            $amount = ($this->amount * $split['percentage']) / 100;
            $this->splits()->create([
                'member_id' => $split['member_id'],
                'amount' => $amount,
                'split_type' => 'percentage'
            ]);
        }
    }
}
