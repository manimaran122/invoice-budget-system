<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-app-dark leading-tight">
            Add Sales Invoice
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-app-card border border-app-border overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('admin.sales-invoices.store') }}" class="p-6 space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="customer_id" value="Customer" />
                        <select id="customer_id" name="customer_id" class="mt-1 block w-full rounded-md border-app-border shadow-sm focus:border-primary focus:ring-primary" required>
                            <option value="">Select customer</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <x-input-label for="invoice_number" value="Invoice Number" />
                            <x-text-input id="invoice_number" name="invoice_number" type="text" class="mt-1 block w-full" value="{{ old('invoice_number') }}" required />
                            <x-input-error :messages="$errors->get('invoice_number')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="invoice_date" value="Invoice Date" />
                            <x-text-input id="invoice_date" name="invoice_date" type="date" class="mt-1 block w-full" value="{{ old('invoice_date') }}" required />
                            <x-input-error :messages="$errors->get('invoice_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="due_date" value="Due Date" />
                            <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full" value="{{ old('due_date') }}" />
                            <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
                        </div>
                    </div>

                    @include('admin.invoices._items-form', ['productServices' => $productServices])

                    <div>
                        <x-input-label for="notes" value="Notes" />
                        <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full rounded-md border-app-border shadow-sm focus:border-primary focus:ring-primary">{{ old('notes') }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.sales-invoices.index') }}" class="text-sm text-app-muted hover:text-app-dark">Cancel</a>
                        <x-primary-button>Save</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>
