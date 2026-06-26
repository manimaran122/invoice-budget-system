<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class CurrencyFreaksService
{
    public function latestRates(): Response
    {
        return Http::timeout(10)->get($this->latestRatesUrl(), [
            'apikey' => config('services.currencyfreaks.key'),
        ]);
    }

    private function latestRatesUrl(): string
    {
        return rtrim((string) config('services.currencyfreaks.base_url'), '/').'/rates/latest';
    }
}
