<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'Cash';
    case BankTransfer = 'Bank Transfer';
    case Card = 'Card';
    case Upi = 'UPI';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
