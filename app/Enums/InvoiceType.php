<?php

namespace App\Enums;

enum InvoiceType: string
{
    case Purchase = 'purchase';
    case Sales = 'sales';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
