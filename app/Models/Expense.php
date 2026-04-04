<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable =['user_id', 'title', 'category', 'amount', 'expense_date', 'receipt_url'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class); // الموظف/المحاسب الذي سجل المصروف
    }
}
