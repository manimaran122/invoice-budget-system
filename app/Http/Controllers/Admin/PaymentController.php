<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InvoiceType;
use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Models\Payment;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class PaymentController extends Controller
{
    public function storePurchase(PaymentRequest $request, PurchaseInvoice $purchaseInvoice): RedirectResponse
    {
        return $this->store($request, $purchaseInvoice, InvoiceType::Purchase->value);
    }

    public function storeSales(PaymentRequest $request, SalesInvoice $salesInvoice): RedirectResponse
    {
        return $this->store($request, $salesInvoice, InvoiceType::Sales->value);
    }

    // Record a payment and refresh the related invoice status in one database step.
    private function store(PaymentRequest $request, PurchaseInvoice|SalesInvoice $invoice, string $invoiceType): RedirectResponse
    {
        $validated = $request->validated();
        $validated['invoice_type'] = $invoiceType;
        $validated['invoice_id'] = $invoice->id;

        try {
            DB::transaction(function () use ($invoice, $validated) {
                Payment::create($validated);
                $invoice->refreshPaymentStatus();
            });
        } catch (Throwable $e) {
            LogHelper::error('Failed to record invoice payment.', $e, ['invoice_type' => $invoiceType, 'invoice_id' => $invoice->id, 'data' => $validated]);

            return back()->withInput()
                ->with('error', 'Unable to record payment. Please try again.');
        }

        return back()->with('success', 'Payment recorded successfully.');
    }
}
