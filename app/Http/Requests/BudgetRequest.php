<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['Monthly', 'Yearly'])],
            'amount' => ['required', 'numeric', 'min:0'],
            'spent' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
