<?php

namespace App\Http\Requests;

use App\Enums\BudgetType;
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
            'type' => ['required', Rule::enum(BudgetType::class)],
            'amount' => ['required', 'numeric', 'min:0'],
            'spent' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
