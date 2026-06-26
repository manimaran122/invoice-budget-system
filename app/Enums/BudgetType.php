<?php

namespace App\Enums;

enum BudgetType: string
{
    case Monthly = 'Monthly';
    case Yearly = 'Yearly';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function badgeClassFor(?string $type): string
    {
        return $type === self::Monthly->value
            ? 'bg-blue-100 text-primary'
            : 'bg-green-100 text-success';
    }
}
