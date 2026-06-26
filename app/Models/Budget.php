<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Budget extends Model
{
    protected $fillable = [
        'name',
        'type',
        'amount',
        'spent',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'spent' => 'decimal:2',
    ];

    public function remaining(): float
    {
        return max((float) $this->amount - (float) $this->spent, 0);
    }

    public function usagePercentage(): float
    {
        if ((float) $this->amount <= 0) {
            return 0;
        }

        return min(((float) $this->spent / (float) $this->amount) * 100, 100);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public static function totalRemaining(): float
    {
        return (float) self::sum('amount') - (float) self::sum('spent');
    }
}
