<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SalesInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $salesInvoiceId = $this->route('sales_invoice')?->id;

        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'invoice_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sales_invoices', 'invoice_number')->ignore($salesInvoiceId),
            ],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:invoice_date'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['Paid', 'Pending', 'Overdue'])],
            'notes' => ['nullable', 'string'],
        ];
    }
}
