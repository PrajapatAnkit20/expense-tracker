<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'created_by',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get the user who created the group
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all members of the group
     */
    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class);
    }

    /**
     * Get all expenses for the group
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(GroupExpense::class);
    }

    /**
     * Get active members only
     */
    public function activeMembers(): HasMany
    {
        return $this->hasMany(GroupMember::class)->where('status', 'active');
    }

    /**
     * Get total group expenses
     */
    public function getTotalExpensesAttribute(): float
    {
        return $this->expenses()->sum('amount');
    }

    /**
     * Get member count
     */
    public function getMemberCountAttribute(): int
    {
        return $this->activeMembers()->count();
    }

    /**
     * Check if user is admin of the group
     */
    public function isAdmin($userId): bool
    {
        return $this->members()
            ->where('user_id', $userId)
            ->where('role', 'admin')
            ->exists();
    }

    /**
     * Check if user is member of the group
     */
    public function isMember($userId): bool
    {
        return $this->members()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->exists();
    }

    /**
     * Get recent expenses
     */
    public function getRecentExpenses($limit = 5)
    {
        return $this->expenses()
            ->with(['paidBy', 'splits.member'])
            ->latest('expense_date')
            ->limit($limit)
            ->get();
    }

    /**
     * Calculate balances for all members
     */
    public function calculateBalances()
    {
        $balances = [];

        foreach ($this->activeMembers as $member) {
            $paid = $this->expenses()
                ->where('paid_by', $member->id)
                ->sum('amount');

            $owes = $member->expenseSplits()
                ->whereHas('groupExpense', function ($query) {
                    $query->where('group_id', $this->id);
                })
                ->sum('amount');

            $balances[$member->id] = [
                'member' => $member,
                'paid' => $paid,
                'owes' => $owes,
                'balance' => $paid - $owes
            ];
        }

        return $balances;
    }
}
