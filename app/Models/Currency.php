<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'display_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function labels(): array
    {
        return self::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->pluck('display_name', 'code')
            ->all();
    }
}
