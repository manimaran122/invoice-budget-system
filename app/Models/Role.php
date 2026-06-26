<?php

namespace App\Models;

use App\Enums\RoleName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public static function admin(): self
    {
        return self::firstOrCreate(['name' => RoleName::Admin->value]);
    }

    public static function user(): self
    {
        return self::firstOrCreate(['name' => RoleName::User->value]);
    }
}
