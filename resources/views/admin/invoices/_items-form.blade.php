@php
    $invoiceItems = old('items');

    if ($invoiceItems === null && isset($invoice)) {
        $invoiceItems = $invoice->items->map(fn ($item) => [
            'product_service_id' => $item->product_service_id,
            'description' => $item->description,
            'quantity' => $item->quantity,
            'price' => $item->price,
            'tax' => $item->tax,
            'discount' => $item->discount,
        ])->toArray();
    }

    $invoiceItems = $invoiceItems ?: [[
        'product_service_id' => '',
        'description' => '',
        'quantity' => 1,
        'price' => 0,
        'tax' => 0,
        'discount' => 0,
    ]];
@endphp

<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h3 class="text-sm font-semibold uppercase tracking-wider text-app-muted">Invoice Items</h3>
        <button type="button" id="add-invoice-item" class="rounded-md bg-primary px-3 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-blue-700">
            + Add Item
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-app-border">
            <thead>
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-app-muted">Product / Service</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-app-muted">Description</th>
                    <th class="px-3 py-2 text-right text-xs font-semibold uppercase text-app-muted">Qty</th>
                    <th class="px-3 py-2 text-right text-xs font-semibold uppercase text-app-muted">Price</th>
                    <th class="px-3 py-2 text-right text-xs font-semibold uppercase text-app-muted">Tax</th>
                    <th class="px-3 py-2 text-right text-xs font-semibold uppercase text-app-muted">Discount</th>
                    <th class="px-3 py-2 text-right text-xs font-semibold uppercase text-app-muted">Total</th>
                    <th class="px-3 py-2 text-right text-xs font-semibold uppercase text-app-muted">Action</th>
                </tr>
            </thead>
            <tbody id="invoice-items-body" class="divide-y divide-app-border">
                @foreach ($invoiceItems as $index => $item)
                    <tr class="invoice-item-row">
                        <td class="px-3 py-3 align-top">
                            <select name="items[{{ $index }}][product_service_id]" class="product-service-select block w-48 rounded-md border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                                <option value="">Manual item</option>
                                @foreach ($productServices as $productService)
                                    <option value="{{ $productService->id }}" data-name="{{ $productService->name }}" data-price="{{ $productService->price }}" data-tax-percentage="{{ $productService->tax_percentage }}" @selected(($item['product_service_id'] ?? '') == $productService->id)>
                                        {{ $productService->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('items.'.$index.'.product_service_id')" class="mt-2" />
                        </td>
                        <td class="px-3 py-3 align-top">
                            <input type="text" name="items[{{ $index }}][description]" class="item-description block w-56 rounded-md border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary" value="{{ $item['description'] ?? '' }}" required>
                            <x-input-error :messages="$errors->get('items.'.$index.'.description')" class="mt-2" />
                        </td>
                        <td class="px-3 py-3 align-top">
                            <input type="number" name="items[{{ $index }}][quantity]" class="item-quantity block w-24 rounded-md border-app-border text-right text-sm shadow-sm focus:border-primary focus:ring-primary" value="{{ $item['quantity'] ?? 1 }}" min="0.01" step="0.01" required>
                            <x-input-error :messages="$errors->get('items.'.$index.'.quantity')" class="mt-2" />
                        </td>
                        <td class="px-3 py-3 align-top">
                            <input type="number" name="items[{{ $index }}][price]" class="item-price block w-28 rounded-md border-app-border text-right text-sm shadow-sm focus:border-primary focus:ring-primary" value="{{ $item['price'] ?? 0 }}" min="0" step="0.01" required>
                            <x-input-error :messages="$errors->get('items.'.$index.'.price')" class="mt-2" />
                        </td>
                        <td class="px-3 py-3 align-top">
                            <input type="number" name="items[{{ $index }}][tax]" class="item-tax block w-28 rounded-md border-app-border text-right text-sm shadow-sm focus:border-primary focus:ring-primary" value="{{ $item['tax'] ?? 0 }}" min="0" step="0.01">
                            <x-input-error :messages="$errors->get('items.'.$index.'.tax')" class="mt-2" />
                        </td>
                        <td class="px-3 py-3 align-top">
                            <input type="number" name="items[{{ $index }}][discount]" class="item-discount block w-28 rounded-md border-app-border text-right text-sm shadow-sm focus:border-primary focus:ring-primary" value="{{ $item['discount'] ?? 0 }}" min="0" step="0.01">
                            <x-input-error :messages="$errors->get('items.'.$index.'.discount')" class="mt-2" />
                        </td>
                        <td class="px-3 py-3 text-right align-top text-sm font-semibold text-app-dark">
                            <span class="item-total">0.00</span>
                        </td>
                        <td class="px-3 py-3 text-right align-top">
                            <button type="button" class="remove-invoice-item rounded-md px-3 py-2 text-sm font-semibold text-danger hover:bg-red-50">Remove</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex justify-end">
        <div class="w-full max-w-sm space-y-2 rounded-md border border-app-border bg-app-background p-4">
            <div class="flex justify-between text-sm text-app-muted">
                <span>Subtotal</span>
                <span id="items-subtotal">0.00</span>
            </div>
            <div class="flex justify-between text-sm text-app-muted">
                <span>Tax</span>
                <span id="items-tax">0.00</span>
            </div>
            <div class="flex justify-between text-sm text-app-muted">
                <span>Discount</span>
                <span id="items-discount">0.00</span>
            </div>
            <div class="flex justify-between border-t border-app-border pt-2 text-lg font-bold text-app-dark">
                <span>Total</span>
                <span id="items-grand-total">0.00</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function () {
            function numberValue(element) {
                return parseFloat($(element).val()) || 0;
            }

            function refreshNames() {
                $('.invoice-item-row').each(function (index) {
                    $(this).find('select, input').each(function () {
                        const name = $(this).attr('name');

                        if (name) {
                            $(this).attr('name', name.replace(/items\[\d+\]/, `items[${index}]`));
                        }
                    });
                });
            }

            function refreshRow(row) {
                const quantity = numberValue(row.find('.item-quantity'));
                const price = numberValue(row.find('.item-price'));
                const tax = numberValue(row.find('.item-tax'));
                const discount = numberValue(row.find('.item-discount'));
                const total = (quantity * price) + tax - discount;

                row.find('.item-total').text(total.toFixed(2));

                return {
                    subtotal: quantity * price,
                    tax: tax,
                    discount: discount,
                    total: total
                };
            }

            function refreshTotals() {
                let subtotal = 0;
                let tax = 0;
                let discount = 0;
                let total = 0;

                $('.invoice-item-row').each(function () {
                    const rowTotals = refreshRow($(this));
                    subtotal += rowTotals.subtotal;
                    tax += rowTotals.tax;
                    discount += rowTotals.discount;
                    total += rowTotals.total;
                });

                $('#items-subtotal').text(subtotal.toFixed(2));
                $('#items-tax').text(tax.toFixed(2));
                $('#items-discount').text(discount.toFixed(2));
                $('#items-grand-total').text(total.toFixed(2));
            }

            $('#add-invoice-item').on('click', function () {
                const row = $('.invoice-item-row:first').clone();
                row.find('input').val('');
                row.find('.item-quantity').val(1);
                row.find('.item-price, .item-tax, .item-discount').val(0);
                row.find('select').val('');
                row.find('.item-total').text('0.00');
                $('#invoice-items-body').append(row);
                refreshNames();
                refreshTotals();
            });

            $(document).on('click', '.remove-invoice-item', function () {
                if ($('.invoice-item-row').length > 1) {
                    $(this).closest('.invoice-item-row').remove();
                    refreshNames();
                    refreshTotals();
                }
            });

            $(document).on('change', '.product-service-select', function () {
                const selected = $(this).find(':selected');
                const row = $(this).closest('.invoice-item-row');
                const price = parseFloat(selected.data('price')) || 0;
                const taxPercentage = parseFloat(selected.data('tax-percentage')) || 0;

                if (selected.val()) {
                    row.find('.item-description').val(selected.data('name'));
                    row.find('.item-price').val(price.toFixed(2));
                    row.find('.item-tax').val(((numberValue(row.find('.item-quantity')) * price * taxPercentage) / 100).toFixed(2));
                }

                refreshTotals();
            });

            $(document).on('input', '.item-quantity, .item-price, .item-tax, .item-discount', refreshTotals);
            refreshTotals();
        });
    </script>
@endpush
