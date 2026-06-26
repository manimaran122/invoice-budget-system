<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'budget_id',
        'title',
        'category',
        'amount',
        'expense_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function scopeBetweenExpenseDates(Builder $query, ?string $fromDate, ?string $toDate): Builder
    {
        return $query
            ->when($fromDate, fn (Builder $query) => $query->whereDate('expense_date', '>=', $fromDate))
            ->when($toDate, fn (Builder $query) => $query->whereDate('expense_date', '<=', $toDate));
    }

    public static function totalAmount(): float
    {
        return (float) self::sum('amount');
    }

    public static function recentList(int $limit = 5)
    {
        return self::with('budget')
            ->latest()
            ->limit($limit)
            ->get();
    }
}
