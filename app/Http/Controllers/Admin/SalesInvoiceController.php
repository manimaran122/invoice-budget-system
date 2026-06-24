<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalesInvoiceRequest;
use App\Models\Customer;
use App\Models\SalesInvoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class SalesInvoiceController extends Controller
{
    public function index(): View
    {
        return view('admin.sales-invoices.index');
    }

    public function data(Request $request)
    {
        $salesInvoices = SalesInvoice::query()
            ->with('customer')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->latest();

        return DataTables::of($salesInvoices)
            ->addIndexColumn()
            ->addColumn('customer_name', fn (SalesInvoice $salesInvoice) => $salesInvoice->customer?->name ?? '-')
            ->editColumn('invoice_date', fn (SalesInvoice $salesInvoice) => $salesInvoice->invoice_date->format('Y-m-d'))
            ->editColumn('due_date', fn (SalesInvoice $salesInvoice) => $salesInvoice->due_date?->format('Y-m-d') ?? '-')
            ->editColumn('total', fn (SalesInvoice $salesInvoice) => number_format($salesInvoice->total, 2))
            ->editColumn('status', function (SalesInvoice $salesInvoice) {
                $class = match ($salesInvoice->status) {
                    'Paid' => 'bg-green-100 text-success',
                    'Overdue' => 'bg-red-100 text-danger',
                    default => 'bg-yellow-100 text-warning',
                };

                return '<span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold '.$class.'">'.$salesInvoice->status.'</span>';
            })
            ->addColumn('action', function (SalesInvoice $salesInvoice) {
                $showUrl = route('admin.sales-invoices.show', $salesInvoice);
                $editUrl = route('admin.sales-invoices.edit', $salesInvoice);
                $deleteUrl = route('admin.sales-invoices.destroy', $salesInvoice);
                $invoiceNumber = e($salesInvoice->invoice_number);
                $csrf = csrf_token();

                return <<<HTML
                    <a href="{$showUrl}" class="text-success hover:text-green-700">View</a>
                    <a href="{$editUrl}" class="ms-3 text-primary hover:text-blue-700">Edit</a>
                    <form method="POST" action="{$deleteUrl}" class="delete-sales-invoice-form inline-block ms-3" data-invoice-number="{$invoiceNumber}">
                        <input type="hidden" name="_token" value="{$csrf}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="text-danger hover:text-red-700">Delete</button>
                    </form>
                HTML;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function show(SalesInvoice $salesInvoice): View
    {
        $salesInvoice->load('customer');

        return view('admin.sales-invoices.show', compact('salesInvoice'));
    }

    public function create(): View
    {
        $customers = Customer::orderBy('name')->get();

        return view('admin.sales-invoices.create', compact('customers'));
    }

    public function store(SalesInvoiceRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['tax'] = $validated['tax'] ?? 0;
        $validated['discount'] = $validated['discount'] ?? 0;
        $validated['total'] = $this->calculateTotal($validated);

        SalesInvoice::create($validated);

        return redirect()
            ->route('admin.sales-invoices.index')
            ->with('success', 'Sales invoice created successfully.');
    }

    public function edit(SalesInvoice $salesInvoice): View
    {
        $customers = Customer::orderBy('name')->get();

        return view('admin.sales-invoices.edit', compact('salesInvoice', 'customers'));
    }

    public function update(SalesInvoiceRequest $request, SalesInvoice $salesInvoice): RedirectResponse
    {
        $validated = $request->validated();
        $validated['tax'] = $validated['tax'] ?? 0;
        $validated['discount'] = $validated['discount'] ?? 0;
        $validated['total'] = $this->calculateTotal($validated);

        $salesInvoice->update($validated);

        return redirect()
            ->route('admin.sales-invoices.index')
            ->with('success', 'Sales invoice updated successfully.');
    }

    public function destroy(SalesInvoice $salesInvoice): RedirectResponse
    {
        $salesInvoice->delete();

        return redirect()
            ->route('admin.sales-invoices.index')
            ->with('success', 'Sales invoice deleted successfully.');
    }

    private function calculateTotal(array $data): float
    {
        return (float) $data['subtotal'] + (float) $data['tax'] - (float) $data['discount'];
    }
}
