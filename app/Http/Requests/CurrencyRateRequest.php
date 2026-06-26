<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CurrencyRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['nullable', 'numeric', 'min:0'],
            'from_currency' => ['nullable', 'string', 'max:10'],
            'to_currency' => ['nullable', 'string', 'max:10'],
        ];
    }
}
