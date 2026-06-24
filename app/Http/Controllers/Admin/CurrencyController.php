<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Throwable;

class CurrencyController extends Controller
{
    public function index(Request $request): View
    {
        $rates = [];
        $convertedRates = [];
        $currencies = [];
        $currencyLabels = $this->currencyLabels();
        $error = null;
        $validated = $request->validate([
            'amount' => ['nullable', 'numeric', 'min:0'],
            'from_currency' => ['nullable', 'string', 'max:10'],
            'to_currency' => ['nullable', 'string', 'max:10'],
        ]);
        $amount = (float) ($validated['amount'] ?? 1);
        $fromCurrency = strtoupper($validated['from_currency'] ?? 'USD');
        $toCurrency = strtoupper($validated['to_currency'] ?? 'INR');
        $convertedAmount = 0;
        $selectedRate = 0;
        $apiKey = config('services.currencyfreaks.key');

        try {
            $response = Http::timeout(10)->get('https://api.currencyfreaks.com/v2.0/rates/latest', [
                'apikey' => $apiKey,
            ]);

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
                $toCurrency = array_key_exists($toCurrency, $rates) ? $toCurrency : 'INR';

                $fromRate = $rates[$fromCurrency];
                $usdAmount = $amount / $fromRate;
                $selectedRate = $rates[$toCurrency] / $fromRate;
                $convertedAmount = $amount * $selectedRate;

                $convertedRates = collect($rates)
                    ->map(fn (float $rate) => $usdAmount * $rate)
                    ->all();
            } else {
                $error = 'Unable to fetch currency rates. Please try again later.';
            }
        } catch (Throwable) {
            $error = 'Unable to fetch currency rates. Please try again later.';
        }

        return view('admin.currency.index', compact(
            'amount',
            'fromCurrency',
            'toCurrency',
            'selectedRate',
            'convertedAmount',
            'currencies',
            'currencyLabels',
            'rates',
            'convertedRates',
            'error',
        ));
    }

    private function currencyLabels(): array
    {
        return [
            'AED' => 'AED - UAE Dirham (United Arab Emirates)',
            'AFN' => 'AFN - Afghani (Afghanistan)',
            'ALL' => 'ALL - Lek (Albania)',
            'AMD' => 'AMD - Dram (Armenia)',
            'ANG' => 'ANG - Guilder (Netherlands Antilles)',
            'AOA' => 'AOA - Kwanza (Angola)',
            'ARS' => 'ARS - Peso (Argentina)',
            'AUD' => 'AUD - Australian Dollar (Australia)',
            'AWG' => 'AWG - Florin (Aruba)',
            'AZN' => 'AZN - Manat (Azerbaijan)',
            'BAM' => 'BAM - Convertible Mark (Bosnia and Herzegovina)',
            'BBD' => 'BBD - Dollar (Barbados)',
            'BDT' => 'BDT - Taka (Bangladesh)',
            'BGN' => 'BGN - Lev (Bulgaria)',
            'BHD' => 'BHD - Dinar (Bahrain)',
            'BIF' => 'BIF - Franc (Burundi)',
            'BMD' => 'BMD - Dollar (Bermuda)',
            'BND' => 'BND - Dollar (Brunei)',
            'BOB' => 'BOB - Boliviano (Bolivia)',
            'BRL' => 'BRL - Real (Brazil)',
            'BSD' => 'BSD - Dollar (Bahamas)',
            'BTN' => 'BTN - Ngultrum (Bhutan)',
            'BWP' => 'BWP - Pula (Botswana)',
            'BYN' => 'BYN - Ruble (Belarus)',
            'BZD' => 'BZD - Dollar (Belize)',
            'CAD' => 'CAD - Canadian Dollar (Canada)',
            'CDF' => 'CDF - Franc (Democratic Republic of the Congo)',
            'CHF' => 'CHF - Franc (Switzerland)',
            'CLP' => 'CLP - Peso (Chile)',
            'CNY' => 'CNY - Yuan (China)',
            'COP' => 'COP - Peso (Colombia)',
            'CRC' => 'CRC - Colon (Costa Rica)',
            'CUP' => 'CUP - Peso (Cuba)',
            'CVE' => 'CVE - Escudo (Cape Verde)',
            'CZK' => 'CZK - Koruna (Czech Republic)',
            'DJF' => 'DJF - Franc (Djibouti)',
            'DKK' => 'DKK - Krone (Denmark)',
            'DOP' => 'DOP - Peso (Dominican Republic)',
            'DZD' => 'DZD - Dinar (Algeria)',
            'EGP' => 'EGP - Pound (Egypt)',
            'ERN' => 'ERN - Nakfa (Eritrea)',
            'ETB' => 'ETB - Birr (Ethiopia)',
            'EUR' => 'EUR - Euro (European Union)',
            'FJD' => 'FJD - Dollar (Fiji)',
            'FKP' => 'FKP - Pound (Falkland Islands)',
            'GBP' => 'GBP - Pound Sterling (United Kingdom)',
            'GEL' => 'GEL - Lari (Georgia)',
            'GHS' => 'GHS - Cedi (Ghana)',
            'GIP' => 'GIP - Pound (Gibraltar)',
            'GMD' => 'GMD - Dalasi (Gambia)',
            'GNF' => 'GNF - Franc (Guinea)',
            'GTQ' => 'GTQ - Quetzal (Guatemala)',
            'GYD' => 'GYD - Dollar (Guyana)',
            'HKD' => 'HKD - Dollar (Hong Kong)',
            'HNL' => 'HNL - Lempira (Honduras)',
            'HRK' => 'HRK - Kuna (Croatia)',
            'HTG' => 'HTG - Gourde (Haiti)',
            'HUF' => 'HUF - Forint (Hungary)',
            'IDR' => 'IDR - Rupiah (Indonesia)',
            'ILS' => 'ILS - Shekel (Israel)',
            'INR' => 'INR - Indian Rupee (India)',
            'IQD' => 'IQD - Dinar (Iraq)',
            'IRR' => 'IRR - Rial (Iran)',
            'ISK' => 'ISK - Krona (Iceland)',
            'JMD' => 'JMD - Dollar (Jamaica)',
            'JOD' => 'JOD - Dinar (Jordan)',
            'JPY' => 'JPY - Yen (Japan)',
            'KES' => 'KES - Shilling (Kenya)',
            'KGS' => 'KGS - Som (Kyrgyzstan)',
            'KHR' => 'KHR - Riel (Cambodia)',
            'KMF' => 'KMF - Franc (Comoros)',
            'KRW' => 'KRW - Won (South Korea)',
            'KWD' => 'KWD - Dinar (Kuwait)',
            'KYD' => 'KYD - Dollar (Cayman Islands)',
            'KZT' => 'KZT - Tenge (Kazakhstan)',
            'LAK' => 'LAK - Kip (Laos)',
            'LBP' => 'LBP - Pound (Lebanon)',
            'LKR' => 'LKR - Rupee (Sri Lanka)',
            'LRD' => 'LRD - Dollar (Liberia)',
            'LSL' => 'LSL - Loti (Lesotho)',
            'LYD' => 'LYD - Dinar (Libya)',
            'MAD' => 'MAD - Dirham (Morocco)',
            'MDL' => 'MDL - Leu (Moldova)',
            'MGA' => 'MGA - Ariary (Madagascar)',
            'MKD' => 'MKD - Denar (North Macedonia)',
            'MMK' => 'MMK - Kyat (Myanmar)',
            'MNT' => 'MNT - Tugrik (Mongolia)',
            'MOP' => 'MOP - Pataca (Macau)',
            'MRU' => 'MRU - Ouguiya (Mauritania)',
            'MUR' => 'MUR - Rupee (Mauritius)',
            'MVR' => 'MVR - Rufiyaa (Maldives)',
            'MWK' => 'MWK - Kwacha (Malawi)',
            'MXN' => 'MXN - Peso (Mexico)',
            'MYR' => 'MYR - Ringgit (Malaysia)',
            'MZN' => 'MZN - Metical (Mozambique)',
            'NAD' => 'NAD - Dollar (Namibia)',
            'NGN' => 'NGN - Naira (Nigeria)',
            'NIO' => 'NIO - Cordoba (Nicaragua)',
            'NOK' => 'NOK - Krone (Norway)',
            'NPR' => 'NPR - Rupee (Nepal)',
            'NZD' => 'NZD - Dollar (New Zealand)',
            'OMR' => 'OMR - Rial (Oman)',
            'PAB' => 'PAB - Balboa (Panama)',
            'PEN' => 'PEN - Sol (Peru)',
            'PGK' => 'PGK - Kina (Papua New Guinea)',
            'PHP' => 'PHP - Peso (Philippines)',
            'PKR' => 'PKR - Rupee (Pakistan)',
            'PLN' => 'PLN - Zloty (Poland)',
            'PYG' => 'PYG - Guarani (Paraguay)',
            'QAR' => 'QAR - Riyal (Qatar)',
            'RON' => 'RON - Leu (Romania)',
            'RSD' => 'RSD - Dinar (Serbia)',
            'RUB' => 'RUB - Ruble (Russia)',
            'RWF' => 'RWF - Franc (Rwanda)',
            'SAR' => 'SAR - Riyal (Saudi Arabia)',
            'SBD' => 'SBD - Dollar (Solomon Islands)',
            'SCR' => 'SCR - Rupee (Seychelles)',
            'SDG' => 'SDG - Pound (Sudan)',
            'SEK' => 'SEK - Krona (Sweden)',
            'SGD' => 'SGD - Dollar (Singapore)',
            'SHP' => 'SHP - Pound (Saint Helena)',
            'SLE' => 'SLE - Leone (Sierra Leone)',
            'SOS' => 'SOS - Shilling (Somalia)',
            'SRD' => 'SRD - Dollar (Suriname)',
            'SSP' => 'SSP - Pound (South Sudan)',
            'STN' => 'STN - Dobra (Sao Tome and Principe)',
            'SYP' => 'SYP - Pound (Syria)',
            'SZL' => 'SZL - Lilangeni (Eswatini)',
            'THB' => 'THB - Baht (Thailand)',
            'TJS' => 'TJS - Somoni (Tajikistan)',
            'TMT' => 'TMT - Manat (Turkmenistan)',
            'TND' => 'TND - Dinar (Tunisia)',
            'TOP' => 'TOP - Paanga (Tonga)',
            'TRY' => 'TRY - Lira (Turkey)',
            'TTD' => 'TTD - Dollar (Trinidad and Tobago)',
            'TWD' => 'TWD - Dollar (Taiwan)',
            'TZS' => 'TZS - Shilling (Tanzania)',
            'UAH' => 'UAH - Hryvnia (Ukraine)',
            'UGX' => 'UGX - Shilling (Uganda)',
            'USD' => 'USD - US Dollar (United States)',
            'UYU' => 'UYU - Peso (Uruguay)',
            'UZS' => 'UZS - Som (Uzbekistan)',
            'VES' => 'VES - Bolivar (Venezuela)',
            'VND' => 'VND - Dong (Vietnam)',
            'VUV' => 'VUV - Vatu (Vanuatu)',
            'WST' => 'WST - Tala (Samoa)',
            'XAF' => 'XAF - CFA Franc (Central Africa)',
            'XCD' => 'XCD - Dollar (Eastern Caribbean)',
            'XOF' => 'XOF - CFA Franc (West Africa)',
            'XPF' => 'XPF - CFP Franc (French Pacific Territories)',
            'YER' => 'YER - Rial (Yemen)',
            'ZAR' => 'ZAR - Rand (South Africa)',
            'ZMW' => 'ZMW - Kwacha (Zambia)',
            'ZWL' => 'ZWL - Dollar (Zimbabwe)',
        ];
    }
}
