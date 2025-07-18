<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'amount',
        'category',
        'category_id',
        'expense_date',
        'user_id',
        'is_group_expense',
        'group_id'
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'is_group_expense' => 'boolean'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
