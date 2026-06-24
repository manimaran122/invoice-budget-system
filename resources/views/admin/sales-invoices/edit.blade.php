<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-app-dark leading-tight">
            Edit Sales Invoice
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-app-card border border-app-border overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('admin.sales-invoices.update', $salesInvoice) }}" class="p-6 space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label for="customer_id" value="Customer" />
                        <select id="customer_id" name="customer_id" class="mt-1 block w-full rounded-md border-app-border shadow-sm focus:border-primary focus:ring-primary" required>
                            <option value="">Select customer</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" @selected(old('customer_id', $salesInvoice->customer_id) == $customer->id)>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <x-input-label for="invoice_number" value="Invoice Number" />
                            <x-text-input id="invoice_number" name="invoice_number" type="text" class="mt-1 block w-full" value="{{ old('invoice_number', $salesInvoice->invoice_number) }}" required />
                            <x-input-error :messages="$errors->get('invoice_number')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="invoice_date" value="Invoice Date" />
                            <x-text-input id="invoice_date" name="invoice_date" type="date" class="mt-1 block w-full" value="{{ old('invoice_date', $salesInvoice->invoice_date->format('Y-m-d')) }}" required />
                            <x-input-error :messages="$errors->get('invoice_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="due_date" value="Due Date" />
                            <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full" value="{{ old('due_date', $salesInvoice->due_date?->format('Y-m-d')) }}" />
                            <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-4">
                        <div>
                            <x-input-label for="subtotal" value="Subtotal" />
                            <x-text-input id="subtotal" name="subtotal" type="number" step="0.01" min="0" class="invoice-amount mt-1 block w-full" value="{{ old('subtotal', $salesInvoice->subtotal) }}" required />
                            <x-input-error :messages="$errors->get('subtotal')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="tax" value="Tax" />
                            <x-text-input id="tax" name="tax" type="number" step="0.01" min="0" class="invoice-amount mt-1 block w-full" value="{{ old('tax', $salesInvoice->tax) }}" />
                            <x-input-error :messages="$errors->get('tax')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="discount" value="Discount" />
                            <x-text-input id="discount" name="discount" type="number" step="0.01" min="0" class="invoice-amount mt-1 block w-full" value="{{ old('discount', $salesInvoice->discount) }}" />
                            <x-input-error :messages="$errors->get('discount')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label value="Total" />
                            <div id="total-preview" class="mt-1 rounded-md border border-app-border bg-app-background px-3 py-2 text-app-dark">{{ number_format($salesInvoice->total, 2) }}</div>
                        </div>
                    </div>

                    <div>
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-app-border shadow-sm focus:border-primary focus:ring-primary" required>
                            <option value="Pending" @selected(old('status', $salesInvoice->status) === 'Pending')>Pending</option>
                            <option value="Paid" @selected(old('status', $salesInvoice->status) === 'Paid')>Paid</option>
                            <option value="Overdue" @selected(old('status', $salesInvoice->status) === 'Overdue')>Overdue</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="notes" value="Notes" />
                        <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full rounded-md border-app-border shadow-sm focus:border-primary focus:ring-primary">{{ old('notes', $salesInvoice->notes) }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.sales-invoices.index') }}" class="text-sm text-app-muted hover:text-app-dark">Cancel</a>
                        <x-primary-button>Update</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                function updateTotal() {
                    const subtotal = parseFloat($('#subtotal').val()) || 0;
                    const tax = parseFloat($('#tax').val()) || 0;
                    const discount = parseFloat($('#discount').val()) || 0;
                    const total = subtotal + tax - discount;

                    $('#total-preview').text(total.toFixed(2));
                }

                $('.invoice-amount').on('input', updateTotal);
                updateTotal();
            });
        </script>
    @endpush
</x-app-layout>
