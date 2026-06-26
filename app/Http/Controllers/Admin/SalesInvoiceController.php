<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SalesInvoiceRequest;
use App\Models\Customer;
use App\Models\ProductService;
use App\Models\SalesInvoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class SalesInvoiceController extends Controller
{
    public function index(): View
    {
        return view('admin.sales-invoices.index');
    }

    public function data(Request $request)
    {
        try {
            $status = in_array($request->status, InvoiceStatus::values(), true) ? $request->status : null;
            $salesInvoices = SalesInvoice::query()
                ->with('customer')
                ->when($status, fn ($query) => $query->where('status', $status))
                ->latest();

            return DataTables::of($salesInvoices)
                ->addIndexColumn()
                ->addColumn('customer_name', fn (SalesInvoice $salesInvoice) => $salesInvoice->customer?->name ?? '-')
                ->editColumn('invoice_date', fn (SalesInvoice $salesInvoice) => $salesInvoice->invoice_date->format('Y-m-d'))
                ->editColumn('due_date', fn (SalesInvoice $salesInvoice) => $salesInvoice->due_date?->format('Y-m-d') ?? '-')
                ->editColumn('total', fn (SalesInvoice $salesInvoice) => number_format($salesInvoice->total, 2))
                ->editColumn('status', function (SalesInvoice $salesInvoice) {
                    $class = InvoiceStatus::badgeClassFor($salesInvoice->status);

                    return '<span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold '.$class.'">'.$salesInvoice->status.'</span>';
                })
                ->addColumn('action', function (SalesInvoice $salesInvoice) {
                    $showUrl = route('admin.sales-invoices.show', $salesInvoice);
                    $editUrl = route('admin.sales-invoices.edit', $salesInvoice);
                    $deleteUrl = route('admin.sales-invoices.destroy', $salesInvoice);
                    $invoiceNumber = e($salesInvoice->invoice_number);
                    $csrf = csrf_token();

                    return <<<HTML
                        <div class="action-buttons">
                        <a href="{$showUrl}" class="action-icon action-view" title="View" aria-label="View sales invoice">&#128065;</a>
                        <a href="{$editUrl}" class="action-icon action-edit" title="Edit" aria-label="Edit sales invoice">&#9998;</a>
                        <form method="POST" action="{$deleteUrl}" class="delete-sales-invoice-form action-form" data-invoice-number="{$invoiceNumber}">
                            <input type="hidden" name="_token" value="{$csrf}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="action-icon action-delete" title="Delete" aria-label="Delete sales invoice">&#128465;</button>
                        </form>
                        </div>
                    HTML;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        } catch (Throwable $e) {
            LogHelper::error('Failed to load sales invoice datatable.', $e);

            return response()->json(['message' => 'Unable to load sales invoices.'], 500);
        }
    }

    public function show(SalesInvoice $salesInvoice): View
    {
        try {
            $salesInvoice->load(['customer', 'items.productService', 'payments']);
        } catch (Throwable $e) {
            LogHelper::error('Failed to show sales invoice.', $e, ['sales_invoice_id' => $salesInvoice->id]);
            abort(500, 'Unable to show sales invoice.');
        }

        return view('admin.sales-invoices.show', compact('salesInvoice'));
    }

    public function create(): View
    {
        try {
            $customers = Customer::orderBy('name')->get();
            $productServices = ProductService::orderBy('name')->get();
        } catch (Throwable $e) {
            LogHelper::error('Failed to load create sales invoice page.', $e);
            abort(500, 'Unable to load sales invoice form.');
        }

        return view('admin.sales-invoices.create', compact('customers', 'productServices'));
    }

    // Save the sales invoice with item rows so totals stay accurate.
    public function store(SalesInvoiceRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $items = $this->prepareItems($validated['items']);
        $totals = $this->calculateTotals($items);
        unset($validated['items']);
        $validated = array_merge($validated, $totals, ['status' => InvoiceStatus::Pending->value]);

        try {
            DB::transaction(function () use ($validated, $items) {
                $salesInvoice = SalesInvoice::create($validated);
                $salesInvoice->items()->createMany($items);
                $salesInvoice->refreshPaymentStatus();
            });
        } catch (Throwable $e) {
            LogHelper::error('Failed to create sales invoice.', $e, ['data' => $validated]);

            return back()->withInput()
                ->with('error', 'Unable to create sales invoice. Please try again.');
        }

        return back()
            ->with('success', 'Sales invoice created successfully.');
    }

    public function edit(SalesInvoice $salesInvoice): View
    {
        try {
            $customers = Customer::orderBy('name')->get();
            $productServices = ProductService::orderBy('name')->get();
            $salesInvoice->load('items');
        } catch (Throwable $e) {
            LogHelper::error('Failed to load edit sales invoice page.', $e, ['sales_invoice_id' => $salesInvoice->id]);
            abort(500, 'Unable to load sales invoice form.');
        }

        return view('admin.sales-invoices.edit', compact('salesInvoice', 'customers', 'productServices'));
    }

    // Rebuild sales invoice item rows and recalculate totals on update.
    public function update(SalesInvoiceRequest $request, SalesInvoice $salesInvoice): RedirectResponse
    {
        $validated = $request->validated();
        $items = $this->prepareItems($validated['items']);
        $totals = $this->calculateTotals($items);
        unset($validated['items']);
        $validated = array_merge($validated, $totals);

        try {
            DB::transaction(function () use ($salesInvoice, $validated, $items) {
                $salesInvoice->update($validated);
                $salesInvoice->items()->delete();
                $salesInvoice->items()->createMany($items);
                $salesInvoice->refreshPaymentStatus();
            });
        } catch (Throwable $e) {
            LogHelper::error('Failed to update sales invoice.', $e, ['sales_invoice_id' => $salesInvoice->id, 'data' => $validated]);

            return back()->withInput()
                ->with('error', 'Unable to update sales invoice. Please try again.');
        }

        return back()
            ->with('success', 'Sales invoice updated successfully.');
    }

    public function destroy(SalesInvoice $salesInvoice): RedirectResponse
    {
        try {
            DB::transaction(function () use ($salesInvoice) {
                $salesInvoice->items()->delete();
                $salesInvoice->payments()->delete();
                $salesInvoice->delete();
            });
        } catch (Throwable $e) {
            LogHelper::error('Failed to delete sales invoice.', $e, ['sales_invoice_id' => $salesInvoice->id]);

            return back()->with('error', 'Unable to delete sales invoice. Please try again.');
        }

        return back()->with('success', 'Sales invoice deleted successfully.');
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
                    'invoice_type' => InvoiceType::Sales->value,
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
