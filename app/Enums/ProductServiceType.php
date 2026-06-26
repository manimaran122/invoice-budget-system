<?php

namespace App\Enums;

enum ProductServiceType: string
{
    case Product = 'Product';
    case Service = 'Service';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function badgeClassFor(?string $type): string
    {
        return $type === self::Product->value
            ? 'bg-blue-100 text-primary'
            : 'bg-green-100 text-success';
    }
}
