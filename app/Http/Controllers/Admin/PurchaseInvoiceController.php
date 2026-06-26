<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseInvoiceRequest;
use App\Models\ProductService;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class PurchaseInvoiceController extends Controller
{
    public function index(): View
    {
        return view('admin.purchase-invoices.index');
    }

    public function data()
    {
        try {
            $purchaseInvoices = PurchaseInvoice::query()
                ->with('supplier')
                ->latest();

            return DataTables::of($purchaseInvoices)
                ->addIndexColumn()
                ->addColumn('supplier_name', fn (PurchaseInvoice $purchaseInvoice) => $purchaseInvoice->supplier?->name ?? '-')
                ->editColumn('invoice_date', fn (PurchaseInvoice $purchaseInvoice) => $purchaseInvoice->invoice_date->format('Y-m-d'))
                ->editColumn('due_date', fn (PurchaseInvoice $purchaseInvoice) => $purchaseInvoice->due_date?->format('Y-m-d') ?? '-')
                ->editColumn('total', fn (PurchaseInvoice $purchaseInvoice) => number_format($purchaseInvoice->total, 2))
                ->editColumn('status', function (PurchaseInvoice $purchaseInvoice) {
                    $class = InvoiceStatus::badgeClassFor($purchaseInvoice->status);

                    return '<span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold '.$class.'">'.$purchaseInvoice->status.'</span>';
                })
                ->addColumn('action', function (PurchaseInvoice $purchaseInvoice) {
                    $showUrl = route('admin.purchase-invoices.show', $purchaseInvoice);
                    $editUrl = route('admin.purchase-invoices.edit', $purchaseInvoice);
                    $deleteUrl = route('admin.purchase-invoices.destroy', $purchaseInvoice);
                    $invoiceNumber = e($purchaseInvoice->invoice_number);
                    $csrf = csrf_token();

                    return <<<HTML
                        <div class="action-buttons">
                        <a href="{$showUrl}" class="action-icon action-view" title="View" aria-label="View purchase invoice">&#128065;</a>
                        <a href="{$editUrl}" class="action-icon action-edit" title="Edit" aria-label="Edit purchase invoice">&#9998;</a>
                        <form method="POST" action="{$deleteUrl}" class="delete-purchase-invoice-form action-form" data-invoice-number="{$invoiceNumber}">
                            <input type="hidden" name="_token" value="{$csrf}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="action-icon action-delete" title="Delete" aria-label="Delete purchase invoice">&#128465;</button>
                        </form>
                        </div>
                    HTML;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        } catch (Throwable $e) {
            LogHelper::error('Failed to load purchase invoice datatable.', $e);

            return response()->json(['message' => 'Unable to load purchase invoices.'], 500);
        }
    }

    public function show(PurchaseInvoice $purchaseInvoice): View
    {
        try {
            $purchaseInvoice->load(['supplier', 'items.productService', 'payments']);
        } catch (Throwable $e) {
            LogHelper::error('Failed to show purchase invoice.', $e, ['purchase_invoice_id' => $purchaseInvoice->id]);
            abort(500, 'Unable to show purchase invoice.');
        }

        return view('admin.purchase-invoices.show', compact('purchaseInvoice'));
    }

    public function create(): View
    {
        try {
            $suppliers = Supplier::orderBy('name')->get();
            $productServices = ProductService::orderBy('name')->get();
        } catch (Throwable $e) {
            LogHelper::error('Failed to load create purchase invoice page.', $e);
            abort(500, 'Unable to load purchase invoice form.');
        }

        return view('admin.purchase-invoices.create', compact('suppliers', 'productServices'));
    }

    // Save the purchase invoice with item rows so totals stay accurate.
    public function store(PurchaseInvoiceRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $items = $this->prepareItems($validated['items']);
        $totals = $this->calculateTotals($items);
        unset($validated['items']);
        $validated = array_merge($validated, $totals, ['status' => InvoiceStatus::Pending->value]);

        try {
            DB::transaction(function () use ($validated, $items) {
                $purchaseInvoice = PurchaseInvoice::create($validated);
                $purchaseInvoice->items()->createMany($items);
                $purchaseInvoice->refreshPaymentStatus();
            });
        } catch (Throwable $e) {
            LogHelper::error('Failed to create purchase invoice.', $e, ['data' => $validated]);

            return back()->withInput()
                ->with('error', 'Unable to create purchase invoice. Please try again.');
        }

        return back()->with('success', 'Purchase invoice created successfully.');
    }

    public function edit(PurchaseInvoice $purchaseInvoice): View
    {
        try {
            $suppliers = Supplier::orderBy('name')->get();
            $productServices = ProductService::orderBy('name')->get();
            $purchaseInvoice->load('items');
        } catch (Throwable $e) {
            LogHelper::error('Failed to load edit purchase invoice page.', $e, ['purchase_invoice_id' => $purchaseInvoice->id]);
            abort(500, 'Unable to load purchase invoice form.');
        }

        return view('admin.purchase-invoices.edit', compact('purchaseInvoice', 'suppliers', 'productServices'));
    }

    // Rebuild purchase invoice item rows and recalculate totals on update.
    public function update(PurchaseInvoiceRequest $request, PurchaseInvoice $purchaseInvoice): RedirectResponse
    {
        $validated = $request->validated();
        $items = $this->prepareItems($validated['items']);
        $totals = $this->calculateTotals($items);
        unset($validated['items']);
        $validated = array_merge($validated, $totals);

        try {
            DB::transaction(function () use ($purchaseInvoice, $validated, $items) {
                $purchaseInvoice->update($validated);
                $purchaseInvoice->items()->delete();
                $purchaseInvoice->items()->createMany($items);
                $purchaseInvoice->refreshPaymentStatus();
            });
        } catch (Throwable $e) {
            LogHelper::error('Failed to update purchase invoice.', $e, ['purchase_invoice_id' => $purchaseInvoice->id, 'data' => $validated]);

            return back()->withInput()
                ->with('error', 'Unable to update purchase invoice. Please try again.');
        }

        return back()->with('success', 'Purchase invoice updated successfully.');
    }

    public function destroy(PurchaseInvoice $purchaseInvoice): RedirectResponse
    {
        try {
            DB::transaction(function () use ($purchaseInvoice) {
                $purchaseInvoice->items()->delete();
                $purchaseInvoice->payments()->delete();
                $purchaseInvoice->delete();
            });
        } catch (Throwable $e) {
            LogHelper::error('Failed to delete purchase invoice.', $e, ['purchase_invoice_id' => $purchaseInvoice->id]);

            return back()->with('error', 'Unable to delete purchase invoice. Please try again.');
        }

        return back()->with('success', 'Purchase invoice deleted successfully.');
    }

    // Normalize submitted item rows before saving them.
    private function prepareItems(array $items): array
    {
        return collect($items)
            ->map(function (array $item) {
                $quantity = (float) $item['quantity'];
                $price = (float) $item['price'];
                $tax = (float) ($item['tax'] ?? 0);
                $discount = (float) ($item['discount'] ?? 0);

                return [
                    'invoice_type' => InvoiceType::Purchase->value,
                    'product_service_id' => $item['product_service_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $quantity,
                    'price' => $price,
                    'tax' => $tax,
                    'discount' => $discount,
                    'total' => ($quantity * $price) + $tax - $discount,
                ];
            })
            ->all();
    }

    // Calculate invoice totals on the server instead of trusting browser input.
    private function calculateTotals(array $items): array
    {
        $subtotal = collect($items)->sum(fn (array $item) => (float) $item['quantity'] * (float) $item['price']);
        $tax = collect($items)->sum('tax');
        $discount = collect($items)->sum('discount');

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $subtotal + $tax - $discount,
        ];
    }
}
