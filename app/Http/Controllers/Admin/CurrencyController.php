<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CurrencyRateRequest;
use App\Models\Currency;
use App\Services\CurrencyFreaksService;
use Illuminate\View\View;
use Throwable;

class CurrencyController extends Controller
{
    public function index(CurrencyRateRequest $request, CurrencyFreaksService $currencyFreaksService): View
    {
        $rates = [];
        $convertedRates = [];
        $currencies = [];
        $currencyLabels = Currency::labels();
        $error = null;
        $validated = $request->validated();
        $amount = (float) ($validated['amount'] ?? 1);
        $fromCurrency = strtoupper($validated['from_currency'] ?? 'USD');
        $toCurrency = strtoupper($validated['to_currency'] ?? 'INR');
        $convertedAmount = 0;
        $selectedRate = 0;

        try {
            $response = $currencyFreaksService->latestRates();

            if ($response->successful() && is_array($response->json('rates'))) {
                $rates = collect($response->json('rates'))
                    ->map(fn ($rate) => (float) $rate)
                    ->filter(fn (float $rate) => $rate > 0)
                    ->only(array_keys($currencyLabels))
                    ->all();

                $rates['USD'] = 1;
                ksort($rates);

                $currencies = array_keys($rates);
                $fromCurrency = array_key_exists($fromCurrency, $rates) ? $fromCurrency : 'USD';
                $toCurrency = array_key_exists($toCurrency, $rates) ? $toCurrency : (array_key_exists('INR', $rates) ? 'INR' : 'USD');

                $fromRate = $rates[$fromCurrency];
                $usdAmount = $amount / $fromRate;
                $selectedRate = $rates[$toCurrency] / $fromRate;
                $convertedAmount = $amount * $selectedRate;

                $convertedRates = collect($rates)->map(fn (float $rate) => $usdAmount * $rate)->all();
            } else {
                LogHelper::warning('Currency API returned an invalid response.', ['status' => $response->status(), 'from_currency' => $fromCurrency, 'to_currency' => $toCurrency]);
                $error = 'Unable to fetch currency rates. Please try again later.';
            }
        } catch (Throwable $e) {
            LogHelper::error('Failed to fetch currency rates.', $e, ['from_currency' => $fromCurrency, 'to_currency' => $toCurrency, 'amount' => $amount]);
            $error = 'Unable to fetch currency rates. Please try again later.';
        }

        return view('admin.currency.index', compact('amount', 'fromCurrency', 'toCurrency', 'selectedRate', 'convertedAmount', 'currencies', 'currencyLabels', 'rates', 'convertedRates', 'error'));
    }
}
