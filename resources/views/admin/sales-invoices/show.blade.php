<x-app-layout>
    @push('styles')
        <style>
            @media print {
                body * {
                    visibility: hidden;
                }

                #invoice-print-area,
                #invoice-print-area * {
                    visibility: visible;
                }

                #invoice-print-area {
                    left: 0;
                    position: absolute;
                    top: 0;
                    width: 100%;
                }

                .no-print {
                    display: none !important;
                }
            }
        </style>
    @endpush

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-app-dark leading-tight">
                Invoice Details
            </h2>

            <a href="{{ route('admin.sales-invoices.index') }}" class="no-print text-sm font-medium text-app-muted hover:text-app-dark">
                Back
            </a>
        </div>
    </x-slot>

    @php
        $statusClass = \App\Enums\InvoiceStatus::badgeClassFor($salesInvoice->status);
    @endphp

    <div class="py-12">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div id="invoice-print-area" class="bg-app-card border border-app-border shadow-sm sm:rounded-lg">
                <div class="border-b border-app-border p-6 sm:p-8">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-widest text-primary">Sales Invoice</p>
                            <h1 class="mt-2 text-3xl font-bold text-app-dark">#{{ $salesInvoice->invoice_number }}</h1>
                            <p class="mt-2 text-sm text-app-muted">Invoice Date: {{ $salesInvoice->invoice_date->format('Y-m-d') }}</p>
                            <p class="text-sm text-app-muted">Due Date: {{ $salesInvoice->due_date?->format('Y-m-d') ?? '-' }}</p>
                        </div>

                        <div class="text-left sm:text-right">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                {{ $salesInvoice->status }}
                            </span>

                            <button type="button" onclick="window.print()" class="no-print mt-4 block rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 sm:ml-auto">
                                Print Invoice
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 border-b border-app-border p-6 sm:grid-cols-2 sm:p-8">
                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-wider text-app-muted">From</h3>
                        <div class="mt-3 text-sm text-app-dark">
                            <p class="font-semibold">{{ Auth::user()->name }}</p>
                            <p>{{ Auth::user()->email }}</p>
                            <p class="text-app-muted">Company / Admin Details</p>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-wider text-app-muted">To</h3>
                        <div class="mt-3 text-sm text-app-dark">
                            <p class="font-semibold">{{ $salesInvoice->customer?->name ?? '-' }}</p>
                            <p>{{ $salesInvoice->customer?->email ?? '-' }}</p>
                            <p>{{ $salesInvoice->customer?->phone ?? '-' }}</p>
                            <p class="text-app-muted">{{ $salesInvoice->customer?->address ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-8">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-app-border">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-app-muted">Product</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-app-muted">Qty</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-app-muted">Price</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-app-muted">Tax</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-app-muted">Discount</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-app-muted">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-app-border">
                                @forelse ($salesInvoice->items as $item)
                                    <tr>
                                        <td class="px-4 py-4 text-sm text-app-dark">
                                            <div class="font-semibold">{{ $item->description }}</div>
                                            <div class="text-xs text-app-muted">{{ $item->productService?->name ?? 'Manual item' }}</div>
                                        </td>
                                        <td class="px-4 py-4 text-right text-sm text-app-dark">{{ number_format($item->quantity, 2) }}</td>
                                        <td class="px-4 py-4 text-right text-sm text-app-dark">{{ number_format($item->price, 2) }}</td>
                                        <td class="px-4 py-4 text-right text-sm text-app-dark">{{ number_format($item->tax, 2) }}</td>
                                        <td class="px-4 py-4 text-right text-sm text-app-dark">{{ number_format($item->discount, 2) }}</td>
                                        <td class="px-4 py-4 text-right text-sm font-semibold text-app-dark">{{ number_format($item->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-6 text-center text-sm text-app-muted">No invoice items found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <div class="w-full max-w-sm space-y-3">
                            <div class="flex justify-between text-sm text-app-muted">
                                <span>Subtotal</span>
                                <span>{{ number_format($salesInvoice->subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-app-muted">
                                <span>Discount</span>
                                <span>{{ number_format($salesInvoice->discount, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-app-muted">
                                <span>Tax</span>
                                <span>{{ number_format($salesInvoice->tax, 2) }}</span>
                            </div>
                            <div class="flex justify-between border-t border-app-border pt-3 text-lg font-bold text-app-dark">
                                <span>Grand Total</span>
                                <span>{{ number_format($salesInvoice->total, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    @if ($salesInvoice->notes)
                        <div class="mt-8 rounded-md border border-app-border bg-app-background p-4 text-sm text-app-muted">
                            {{ $salesInvoice->notes }}
                        </div>
                    @endif

                    <div class="no-print mt-8 flex justify-end gap-3">
                        <a href="{{ route('admin.sales-invoices.index') }}" class="rounded-md border border-app-border px-4 py-2 text-sm font-semibold text-app-muted hover:bg-app-background">
                            Back
                        </a>
                        <button type="button" onclick="window.print()" class="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                            Print Invoice
                        </button>
                    </div>
                </div>
            </div>

            @include('admin.invoices._payments', ['invoice' => $salesInvoice, 'paymentRoute' => route('admin.sales-invoices.payments.store', $salesInvoice)])
        </div>
    </div>
</x-app-layout>
