<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-app-dark leading-tight">
                Reports & Analytics
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <div class="bg-app-card border border-app-border shadow-sm sm:rounded-lg">
                <form method="GET" action="{{ route('admin.reports.index') }}" class="grid gap-4 p-6 md:grid-cols-4 md:items-end">
                    <div>
                        <x-input-label for="from_date" value="From Date" />
                        <x-text-input id="from_date" name="from_date" type="date" class="mt-1 block w-full" value="{{ $fromDate }}" />
                        <x-input-error :messages="$errors->get('from_date')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="to_date" value="To Date" />
                        <x-text-input id="to_date" name="to_date" type="date" class="mt-1 block w-full" value="{{ $toDate }}" />
                        <x-input-error :messages="$errors->get('to_date')" class="mt-2" />
                    </div>

                    <div class="flex gap-3 md:col-span-2">
                        <x-primary-button>Generate Report</x-primary-button>
                        <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center rounded-md border border-app-border px-4 py-2 text-xs font-semibold uppercase tracking-widest text-app-muted hover:bg-app-background">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                <div class="bg-app-card border border-app-border p-6 shadow-sm sm:rounded-lg">
                    <p class="text-sm font-medium text-app-muted">Total Sales</p>
                    <p class="mt-3 text-2xl font-bold text-success">{{ number_format($totalSales, 2) }}</p>
                </div>

                <div class="bg-app-card border border-app-border p-6 shadow-sm sm:rounded-lg">
                    <p class="text-sm font-medium text-app-muted">Total Purchase</p>
                    <p class="mt-3 text-2xl font-bold text-danger">{{ number_format($totalPurchase, 2) }}</p>
                </div>

                <div class="bg-app-card border border-app-border p-6 shadow-sm sm:rounded-lg">
                    <p class="text-sm font-medium text-app-muted">Total Expense</p>
                    <p class="mt-3 text-2xl font-bold text-warning">{{ number_format($totalExpense, 2) }}</p>
                </div>

                <div class="bg-app-card border border-app-border p-6 shadow-sm sm:rounded-lg">
                    <p class="text-sm font-medium text-app-muted">Profit / Loss</p>
                    <p class="mt-3 text-2xl font-bold {{ $profitLoss >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($profitLoss, 2) }}</p>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-2">
                <div class="bg-app-card border border-app-border shadow-sm sm:rounded-lg">
                    <div class="border-b border-app-border p-6">
                        <h3 class="font-semibold text-app-dark">Invoice Status Summary</h3>
                    </div>

                    <div class="overflow-x-auto p-6">
                        <table class="min-w-full divide-y divide-app-border">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-app-muted">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-app-muted">Sales Invoices</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-app-muted">Purchase Invoices</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-app-border">
                                @foreach ($invoiceStatusSummary as $item)
                                    @php
                                        $statusClass = match ($item['status']) {
                                            'Paid' => 'bg-green-100 text-success',
                                            'Overdue' => 'bg-red-100 text-danger',
                                            default => 'bg-yellow-100 text-warning',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-4">
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">{{ $item['status'] }}</span>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-app-dark">{{ $item['sales_count'] }}</td>
                                        <td class="px-4 py-4 text-sm text-app-dark">{{ $item['purchase_count'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-app-card border border-app-border shadow-sm sm:rounded-lg">
                    <div class="border-b border-app-border p-6">
                        <h3 class="font-semibold text-app-dark">Budget vs Expense Summary</h3>
                    </div>

                    <div class="overflow-x-auto p-6">
                        <table class="min-w-full divide-y divide-app-border">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-app-muted">Budget</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-app-muted">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-app-muted">Spent</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-app-muted">Remaining</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-app-muted">Usage</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-app-border">
                                @forelse ($budgetSummary as $budget)
                                    @php
                                        $barClass = $budget['usage'] >= 90 ? 'bg-danger' : ($budget['usage'] >= 70 ? 'bg-warning' : 'bg-success');
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-4">
                                            <p class="text-sm font-semibold text-app-dark">{{ $budget['name'] }}</p>
                                            <p class="text-xs text-app-muted">{{ $budget['type'] }}</p>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-app-dark">{{ number_format($budget['amount'], 2) }}</td>
                                        <td class="px-4 py-4 text-sm text-app-dark">{{ number_format($budget['spent'], 2) }}</td>
                                        <td class="px-4 py-4 text-sm text-app-dark">{{ number_format($budget['remaining'], 2) }}</td>
                                        <td class="px-4 py-4">
                                            <div class="flex min-w-40 items-center gap-3">
                                                <div class="h-2 w-28 overflow-hidden rounded-full bg-app-border">
                                                    <div class="h-full {{ $barClass }}" style="width: {{ $budget['usage'] }}%"></div>
                                                </div>
                                                <span class="text-sm font-semibold text-app-dark">{{ $budget['usage'] }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-sm text-app-muted">No budgets found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
