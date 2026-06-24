<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseInvoiceRequest;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class PurchaseInvoiceController extends Controller
{
    public function index(): View
    {
        return view('admin.purchase-invoices.index');
    }

    public function data()
    {
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
                $class = match ($purchaseInvoice->status) {
                    'Paid' => 'bg-green-100 text-success',
                    'Overdue' => 'bg-red-100 text-danger',
                    default => 'bg-yellow-100 text-warning',
                };

                return '<span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold '.$class.'">'.$purchaseInvoice->status.'</span>';
            })
            ->addColumn('action', function (PurchaseInvoice $purchaseInvoice) {
                $showUrl = route('admin.purchase-invoices.show', $purchaseInvoice);
                $editUrl = route('admin.purchase-invoices.edit', $purchaseInvoice);
                $deleteUrl = route('admin.purchase-invoices.destroy', $purchaseInvoice);
                $invoiceNumber = e($purchaseInvoice->invoice_number);
                $csrf = csrf_token();

                return <<<HTML
                    <a href="{$showUrl}" class="text-success hover:text-green-700">View</a>
                    <a href="{$editUrl}" class="ms-3 text-primary hover:text-blue-700">Edit</a>
                    <form method="POST" action="{$deleteUrl}" class="delete-purchase-invoice-form inline-block ms-3" data-invoice-number="{$invoiceNumber}">
                        <input type="hidden" name="_token" value="{$csrf}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="text-danger hover:text-red-700">Delete</button>
                    </form>
                HTML;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function show(PurchaseInvoice $purchaseInvoice): View
    {
        $purchaseInvoice->load('supplier');

        return view('admin.purchase-invoices.show', compact('purchaseInvoice'));
    }

    public function create(): View
    {
        $suppliers = Supplier::orderBy('name')->get();

        return view('admin.purchase-invoices.create', compact('suppliers'));
    }

    public function store(PurchaseInvoiceRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['tax'] = $validated['tax'] ?? 0;
        $validated['discount'] = $validated['discount'] ?? 0;
        $validated['total'] = $this->calculateTotal($validated);

        PurchaseInvoice::create($validated);

        return redirect()
            ->route('admin.purchase-invoices.index')
            ->with('success', 'Purchase invoice created successfully.');
    }

    public function edit(PurchaseInvoice $purchaseInvoice): View
    {
        $suppliers = Supplier::orderBy('name')->get();

        return view('admin.purchase-invoices.edit', compact('purchaseInvoice', 'suppliers'));
    }

    public function update(PurchaseInvoiceRequest $request, PurchaseInvoice $purchaseInvoice): RedirectResponse
    {
        $validated = $request->validated();
        $validated['tax'] = $validated['tax'] ?? 0;
        $validated['discount'] = $validated['discount'] ?? 0;
        $validated['total'] = $this->calculateTotal($validated);

        $purchaseInvoice->update($validated);

        return redirect()
            ->route('admin.purchase-invoices.index')
            ->with('success', 'Purchase invoice updated successfully.');
    }

    public function destroy(PurchaseInvoice $purchaseInvoice): RedirectResponse
    {
        $purchaseInvoice->delete();

        return redirect()
            ->route('admin.purchase-invoices.index')
            ->with('success', 'Purchase invoice deleted successfully.');
    }

    private function calculateTotal(array $data): float
    {
        return (float) $data['subtotal'] + (float) $data['tax'] - (float) $data['discount'];
    }
}
