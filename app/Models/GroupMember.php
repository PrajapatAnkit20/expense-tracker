<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'user_id',
        'name',
        'email',
        'phone',
        'role',
        'status',
        'joined_at'
    ];

    protected $casts = [
        'joined_at' => 'datetime'
    ];

    /**
     * Get the group this member belongs to
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the user (if registered)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get expenses paid by this member
     */
    public function expensesPaid(): HasMany
    {
        return $this->hasMany(GroupExpense::class, 'paid_by');
    }

    /**
     * Get expense splits for this member
     */
    public function expenseSplits(): HasMany
    {
        return $this->hasMany(GroupExpenseSplit::class, 'member_id');
    }

    /**
     * Get display name
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->user ? $this->user->name : $this->name;
    }

    /**
     * Get member's avatar
     */
    public function getAvatarAttribute(): string
    {
        if ($this->user && $this->user->avatar) {
            return $this->user->avatar;
        }

        // Generate avatar based on name
        $name = $this->display_name;
        $initials = strtoupper(substr($name, 0, 1));
        if (strpos($name, ' ') !== false) {
            $initials .= strtoupper(substr($name, strpos($name, ' ') + 1, 1));
        }

        return "https://ui-avatars.com/api/?name={$initials}&background=random&color=fff&size=40";
    }

    /**
     * Check if member is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if member is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get total amount paid by this member
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->expensesPaid()->sum('amount');
    }

    /**
     * Get total amount owed by this member
     */
    public function getTotalOwedAttribute(): float
    {
        return $this->expenseSplits()->sum('amount');
    }

    /**
     * Get balance for this member
     */
    public function getBalanceAttribute(): float
    {
        return $this->total_paid - $this->total_owed;
    }
}
