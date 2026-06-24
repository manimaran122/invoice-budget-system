<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-app-dark leading-tight">
                Admin Dashboard
            </h2>
            <p class="mt-1 text-sm text-app-muted">
                Financial summary for invoices, budgets, and expenses.
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
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
                    <p class="text-sm font-medium text-app-muted">Total Expenses</p>
                    <p class="mt-3 text-2xl font-bold text-warning">{{ number_format($totalExpenses, 2) }}</p>
                </div>

                <div class="bg-app-card border border-app-border p-6 shadow-sm sm:rounded-lg">
                    <p class="text-sm font-medium text-app-muted">Remaining Budget</p>
                    <p class="mt-3 text-2xl font-bold text-primary">{{ number_format($remainingBudget, 2) }}</p>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <div class="bg-app-card border border-app-border p-6 shadow-sm sm:rounded-lg">
                    <p class="text-sm font-medium text-app-muted">Pending Invoices</p>
                    <p class="mt-3 text-3xl font-bold text-warning">{{ $pendingInvoices }}</p>
                </div>

                <div class="bg-app-card border border-app-border p-6 shadow-sm sm:rounded-lg">
                    <p class="text-sm font-medium text-app-muted">Paid Invoices</p>
                    <p class="mt-3 text-3xl font-bold text-success">{{ $paidInvoices }}</p>
                </div>

                <div class="bg-app-card border border-app-border p-6 shadow-sm sm:rounded-lg">
                    <p class="text-sm font-medium text-app-muted">Overdue Invoices</p>
                    <p class="mt-3 text-3xl font-bold text-danger">{{ $overdueInvoices }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
