<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchaseInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $purchaseInvoiceId = $this->route('purchase_invoice')?->id;

        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'invoice_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('purchase_invoices', 'invoice_number')->ignore($purchaseInvoiceId),
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
