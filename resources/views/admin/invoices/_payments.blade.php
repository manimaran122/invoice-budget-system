@php
    $paidAmount = $invoice->paidAmount();
    $balanceAmount = max((float) $invoice->total - $paidAmount, 0);
@endphp

<div class="no-print mt-6 grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
    <div class="rounded-lg border border-app-border bg-app-card p-6 shadow-sm">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-widest text-primary">Payment Status</p>
                <h3 class="mt-2 text-xl font-bold text-app-dark">Payments</h3>
            </div>
            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-primary">
                {{ $invoice->status }}
            </span>
        </div>

        <div class="mt-5 grid gap-3 sm:grid-cols-3">
            <div class="rounded-lg border border-app-border bg-app-background p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-app-muted">Total</p>
                <p class="mt-2 text-lg font-bold text-app-dark">{{ number_format($invoice->total, 2) }}</p>
            </div>
            <div class="rounded-lg border border-green-100 bg-green-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-success">Paid</p>
                <p class="mt-2 text-lg font-bold text-success">{{ number_format($paidAmount, 2) }}</p>
            </div>
            <div class="rounded-lg border border-red-100 bg-red-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-danger">Balance</p>
                <p class="mt-2 text-lg font-bold text-danger">{{ number_format($balanceAmount, 2) }}</p>
            </div>
        </div>

        <div class="mt-6 overflow-hidden rounded-lg border border-app-border">
            <table class="min-w-full divide-y divide-app-border">
                <thead class="bg-app-background">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-app-muted">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-app-muted">Method</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-app-muted">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-app-border bg-white">
                    @forelse ($invoice->payments as $payment)
                        <tr class="hover:bg-app-background">
                            <td class="px-4 py-3 text-sm text-app-dark">{{ $payment->payment_date->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 text-sm text-app-dark">
                                <div class="font-semibold">{{ $payment->payment_method }}</div>
                                <div class="text-xs text-app-muted">{{ $payment->notes ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-semibold text-app-dark">{{ number_format($payment->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-sm text-app-muted">No payments recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-lg border border-app-border bg-app-card p-6 shadow-sm">
        <div>
            <p class="text-sm font-semibold uppercase tracking-widest text-primary">Record Payment</p>
            <h3 class="mt-2 text-xl font-bold text-app-dark">Add Payment</h3>
            <p class="mt-1 text-sm text-app-muted">Enter the received or paid amount for this invoice.</p>
        </div>

        <form method="POST" action="{{ $paymentRoute }}" class="mt-6 space-y-4">
            @csrf

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="amount" value="Amount" />
                    <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.01" class="mt-1 block w-full" value="{{ old('amount', $balanceAmount > 0 ? number_format($balanceAmount, 2, '.', '') : '') }}" required />
                    <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="payment_date" value="Payment Date" />
                    <x-text-input id="payment_date" name="payment_date" type="date" class="mt-1 block w-full" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required />
                    <x-input-error :messages="$errors->get('payment_date')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="payment_method" value="Method" />
                <select id="payment_method" name="payment_method" class="mt-1 block w-full rounded-md border-app-border shadow-sm focus:border-primary focus:ring-primary" required>
                    @foreach (\App\Enums\PaymentMethod::cases() as $method)
                        <option value="{{ $method->value }}" @selected(old('payment_method') === $method->value)>{{ $method->value }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="notes" value="Notes" />
                <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-app-border shadow-sm focus:border-primary focus:ring-primary">{{ old('notes') }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>

            <div class="flex justify-end">
                <x-primary-button>Record Payment</x-primary-button>
            </div>
        </form>
    </div>
</div>
