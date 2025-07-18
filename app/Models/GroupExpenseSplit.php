<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupExpenseSplit extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_expense_id',
        'member_id',
        'amount',
        'split_type',
        'is_paid',
        'paid_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_paid' => 'boolean',
        'paid_at' => 'datetime'
    ];

    /**
     * Get the expense this split belongs to
     */
    public function groupExpense(): BelongsTo
    {
        return $this->belongsTo(GroupExpense::class);
    }

    /**
     * Get the member this split belongs to
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(GroupMember::class);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₹' . number_format($this->amount, 2);
    }

    /**
     * Mark split as paid
     */
    public function markAsPaid(): void
    {
        $this->update([
            'is_paid' => true,
            'paid_at' => now()
        ]);
    }

    /**
     * Mark split as unpaid
     */
    public function markAsUnpaid(): void
    {
        $this->update([
            'is_paid' => false,
            'paid_at' => null
        ]);
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute(): string
    {
        return $this->is_paid ? 'Paid' : 'Pending';
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        return $this->is_paid ? '#10b981' : '#f59e0b';
    }
}
