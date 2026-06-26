<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-app-dark leading-tight">
                    Dashboard
                </h2>
                <p class="mt-1 text-sm text-app-muted">
                    Financial summary for invoices, budgets, expenses, and payments.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.sales-invoices.create') }}" class="rounded-md bg-primary px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-blue-700">
                    + Sales Invoice
                </a>
                <a href="{{ route('admin.purchase-invoices.create') }}" class="rounded-md border border-app-border bg-app-card px-4 py-2 text-xs font-semibold uppercase tracking-widest text-app-dark hover:bg-app-background">
                    + Purchase Invoice
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                <div class="bg-app-card border border-app-border p-6 shadow-sm sm:rounded-lg">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-app-muted">Total Sales</p>
                        <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-success">Sales</span>
                    </div>
                    <p class="mt-3 text-2xl font-bold text-success">{{ number_format($totalSales, 2) }}</p>
                    <p class="mt-2 text-xs text-app-muted">Sales invoice total</p>
                </div>

                <div class="bg-app-card border border-app-border p-6 shadow-sm sm:rounded-lg">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-app-muted">Total Purchase</p>
                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-danger">Purchase</span>
                    </div>
                    <p class="mt-3 text-2xl font-bold text-danger">{{ number_format($totalPurchase, 2) }}</p>
                    <p class="mt-2 text-xs text-app-muted">Purchase invoice total</p>
                </div>

                <div class="bg-app-card border border-app-border p-6 shadow-sm sm:rounded-lg">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-app-muted">Total Expenses</p>
                        <span class="rounded-full bg-yellow-50 px-3 py-1 text-xs font-semibold text-warning">Expense</span>
                    </div>
                    <p class="mt-3 text-2xl font-bold text-warning">{{ number_format($totalExpenses, 2) }}</p>
                    <p class="mt-2 text-xs text-app-muted">Budget expense total</p>
                </div>

                <div class="bg-app-card border border-app-border p-6 shadow-sm sm:rounded-lg">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-app-muted">Profit / Loss</p>
                        <span class="rounded-full {{ $profitLoss >= 0 ? 'bg-green-50 text-success' : 'bg-red-50 text-danger' }} px-3 py-1 text-xs font-semibold">
                            {{ $profitLoss >= 0 ? 'Profit' : 'Loss' }}
                        </span>
                    </div>
                    <p class="mt-3 text-2xl font-bold {{ $profitLoss >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($profitLoss, 2) }}</p>
                    <p class="mt-2 text-xs text-app-muted">Sales - purchase - expenses</p>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                <div class="bg-app-card border border-app-border p-6 shadow-sm sm:rounded-lg">
                    <p class="text-sm font-medium text-app-muted">Sales Payments Received</p>
                    <p class="mt-3 text-2xl font-bold text-success">{{ number_format($salesPayments, 2) }}</p>
                </div>

                <div class="bg-app-card border border-app-border p-6 shadow-sm sm:rounded-lg">
                    <p class="text-sm font-medium text-app-muted">Purchase Payments Paid</p>
                    <p class="mt-3 text-2xl font-bold text-danger">{{ number_format($purchasePayments, 2) }}</p>
                </div>

                <div class="bg-app-card border border-app-border p-6 shadow-sm sm:rounded-lg">
                    <p class="text-sm font-medium text-app-muted">Outstanding Sales Balance</p>
                    <p class="mt-3 text-2xl font-bold text-primary">{{ number_format($outstandingSales, 2) }}</p>
                </div>

                <div class="bg-app-card border border-app-border p-6 shadow-sm sm:rounded-lg">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-app-muted">Collection Rate</p>
                        <span class="text-sm font-bold text-app-dark">{{ $collectionRate }}%</span>
                    </div>
                    <div class="mt-4 h-2 overflow-hidden rounded-full bg-app-border">
                        <div class="h-full bg-primary" style="width: {{ $collectionRate }}%"></div>
                    </div>
                    <p class="mt-2 text-xs text-app-muted">Paid invoices against total invoices</p>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <div class="bg-app-card border border-app-border p-6 shadow-sm sm:rounded-lg">
                    <p class="text-sm font-medium text-app-muted">{{ \App\Enums\InvoiceStatus::Pending->value }} Invoices</p>
                    <p class="mt-3 text-3xl font-bold text-warning">{{ $invoiceStatusSummary[\App\Enums\InvoiceStatus::Pending->value] }}</p>
                </div>

                <div class="bg-app-card border border-app-border p-6 shadow-sm sm:rounded-lg">
                    <p class="text-sm font-medium text-app-muted">{{ \App\Enums\InvoiceStatus::Paid->value }} Invoices</p>
                    <p class="mt-3 text-3xl font-bold text-success">{{ $invoiceStatusSummary[\App\Enums\InvoiceStatus::Paid->value] }}</p>
                </div>

                <div class="bg-app-card border border-app-border p-6 shadow-sm sm:rounded-lg">
                    <p class="text-sm font-medium text-app-muted">{{ \App\Enums\InvoiceStatus::Overdue->value }} Invoices</p>
                    <p class="mt-3 text-3xl font-bold text-danger">{{ $invoiceStatusSummary[\App\Enums\InvoiceStatus::Overdue->value] }}</p>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-2">
                <div class="bg-app-card border border-app-border shadow-sm sm:rounded-lg">
                    <div class="flex items-center justify-between border-b border-app-border p-6">
                        <div>
                            <h3 class="font-semibold text-app-dark">Recent Sales Invoices</h3>
                            <p class="mt-1 text-sm text-app-muted">Latest customer invoice records.</p>
                        </div>
                        <a href="{{ route('admin.sales-invoices.index') }}" class="text-sm font-semibold text-primary hover:text-blue-700">View All</a>
                    </div>

                    <div class="overflow-x-auto p-6">
                        <table class="min-w-full divide-y divide-app-border">
                            <thead class="bg-app-background">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-app-muted">Invoice No</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-app-muted">Customer</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase text-app-muted">Total</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase text-app-muted">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-app-border">
                                @forelse ($recentSalesInvoices as $invoice)
                                    @php
                                        $statusClass = \App\Enums\InvoiceStatus::badgeClassFor($invoice->status);
                                    @endphp
                                    <tr class="hover:bg-app-background">
                                        <td class="px-4 py-4 text-sm font-semibold text-app-dark">{{ $invoice->invoice_number }}</td>
                                        <td class="px-4 py-4 text-sm text-app-dark">{{ $invoice->customer?->name ?? '-' }}</td>
                                        <td class="px-4 py-4 text-right text-sm text-app-dark">{{ number_format($invoice->total, 2) }}</td>
                                        <td class="px-4 py-4 text-right">
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">{{ $invoice->status }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-sm text-app-muted">No sales invoices found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-app-card border border-app-border shadow-sm sm:rounded-lg">
                    <div class="flex items-center justify-between border-b border-app-border p-6">
                        <div>
                            <h3 class="font-semibold text-app-dark">Recent Purchase Invoices</h3>
                            <p class="mt-1 text-sm text-app-muted">Latest supplier invoice records.</p>
                        </div>
                        <a href="{{ route('admin.purchase-invoices.index') }}" class="text-sm font-semibold text-primary hover:text-blue-700">View All</a>
                    </div>

                    <div class="overflow-x-auto p-6">
                        <table class="min-w-full divide-y divide-app-border">
                            <thead class="bg-app-background">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-app-muted">Invoice No</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-app-muted">Supplier</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase text-app-muted">Total</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase text-app-muted">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-app-border">
                                @forelse ($recentPurchaseInvoices as $invoice)
                                    @php
                                        $statusClass = \App\Enums\InvoiceStatus::badgeClassFor($invoice->status);
                                    @endphp
                                    <tr class="hover:bg-app-background">
                                        <td class="px-4 py-4 text-sm font-semibold text-app-dark">{{ $invoice->invoice_number }}</td>
                                        <td class="px-4 py-4 text-sm text-app-dark">{{ $invoice->supplier?->name ?? '-' }}</td>
                                        <td class="px-4 py-4 text-right text-sm text-app-dark">{{ number_format($invoice->total, 2) }}</td>
                                        <td class="px-4 py-4 text-right">
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">{{ $invoice->status }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-sm text-app-muted">No purchase invoices found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-2">
                <div class="bg-app-card border border-app-border shadow-sm sm:rounded-lg">
                    <div class="flex items-center justify-between border-b border-app-border p-6">
                        <div>
                            <h3 class="font-semibold text-app-dark">Budget Usage</h3>
                            <p class="mt-1 text-sm text-app-muted">Track expenses against budget limits.</p>
                        </div>
                        <a href="{{ route('admin.budgets.index') }}" class="text-sm font-semibold text-primary hover:text-blue-700">Manage</a>
                    </div>

                    <div class="space-y-5 p-6">
                        @forelse ($budgetUsages as $budget)
                            @php
                                $usage = round($budget->usagePercentage());
                                $barClass = $usage >= 90 ? 'bg-danger' : ($usage >= 70 ? 'bg-warning' : 'bg-success');
                            @endphp
                            <div>
                                <div class="mb-2 flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-app-dark">{{ $budget->name }}</p>
                                        <p class="text-xs text-app-muted">{{ $budget->type }}</p>
                                    </div>
                                    <span class="text-sm font-bold text-app-dark">{{ $usage }}%</span>
                                </div>
                                <div class="h-2 overflow-hidden rounded-full bg-app-border">
                                    <div class="h-full {{ $barClass }}" style="width: {{ $usage }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-app-muted">No budgets found.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-app-card border border-app-border shadow-sm sm:rounded-lg">
                    <div class="flex items-center justify-between border-b border-app-border p-6">
                        <div>
                            <h3 class="font-semibold text-app-dark">Quick Actions</h3>
                            <p class="mt-1 text-sm text-app-muted">Common admin tasks.</p>
                        </div>
                    </div>

                    <div class="grid gap-3 p-6 sm:grid-cols-2">
                        <a href="{{ route('admin.customers.create') }}" class="rounded-lg border border-app-border p-4 text-sm font-semibold text-app-dark hover:bg-app-background">Add Customer</a>
                        <a href="{{ route('admin.suppliers.create') }}" class="rounded-lg border border-app-border p-4 text-sm font-semibold text-app-dark hover:bg-app-background">Add Supplier</a>
                        <a href="{{ route('admin.product-services.create') }}" class="rounded-lg border border-app-border p-4 text-sm font-semibold text-app-dark hover:bg-app-background">Add Product / Service</a>
                        <a href="{{ route('admin.expenses.create') }}" class="rounded-lg border border-app-border p-4 text-sm font-semibold text-app-dark hover:bg-app-background">Add Expense</a>
                        <a href="{{ route('admin.reports.index') }}" class="rounded-lg border border-app-border p-4 text-sm font-semibold text-app-dark hover:bg-app-background">View Reports</a>
                        <a href="{{ route('admin.currency.index') }}" class="rounded-lg border border-app-border p-4 text-sm font-semibold text-app-dark hover:bg-app-background">Currency Rates</a>
                    </div>
                </div>
            </div>

            <div class="bg-app-card border border-app-border shadow-sm sm:rounded-lg">
                <div class="flex items-center justify-between border-b border-app-border p-6">
                    <div>
                        <h3 class="font-semibold text-app-dark">Recent Expenses</h3>
                        <p class="mt-1 text-sm text-app-muted">Latest expense entries.</p>
                    </div>
                    <a href="{{ route('admin.expenses.index') }}" class="text-sm font-semibold text-primary hover:text-blue-700">View All</a>
                </div>

                <div class="overflow-x-auto p-6">
                    <table class="min-w-full divide-y divide-app-border">
                        <thead class="bg-app-background">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-app-muted">Title</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-app-muted">Budget</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase text-app-muted">Amount</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase text-app-muted">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-app-border">
                            @forelse ($recentExpenses as $expense)
                                <tr class="hover:bg-app-background">
                                    <td class="px-4 py-4 text-sm font-semibold text-app-dark">{{ $expense->title }}</td>
                                    <td class="px-4 py-4 text-sm text-app-dark">{{ $expense->budget?->name ?? '-' }}</td>
                                    <td class="px-4 py-4 text-right text-sm text-app-dark">{{ number_format($expense->amount, 2) }}</td>
                                    <td class="px-4 py-4 text-right text-sm text-app-muted">{{ $expense->expense_date->format('Y-m-d') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-sm text-app-muted">No expenses found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
