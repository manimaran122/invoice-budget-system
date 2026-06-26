<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case Paid = 'Paid';
    case Pending = 'Pending';
    case Overdue = 'Overdue';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function badgeClassFor(?string $status): string
    {
        return match ($status) {
            self::Paid->value => 'bg-green-100 text-success',
            self::Overdue->value => 'bg-red-100 text-danger',
            default => 'bg-yellow-100 text-warning',
        };
    }
}
