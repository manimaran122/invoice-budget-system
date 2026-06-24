<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\Expense;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
        ]);

        $fromDate = $validated['from_date'] ?? null;
        $toDate = $validated['to_date'] ?? null;

        $salesQuery = SalesInvoice::query();
        $purchaseQuery = PurchaseInvoice::query();
        $expenseQuery = Expense::query();

        $this->applyDateFilter($salesQuery, 'invoice_date', $fromDate, $toDate);
        $this->applyDateFilter($purchaseQuery, 'invoice_date', $fromDate, $toDate);
        $this->applyDateFilter($expenseQuery, 'expense_date', $fromDate, $toDate);

        $totalSales = (float) $salesQuery->sum('total');
        $totalPurchase = (float) $purchaseQuery->sum('total');
        $totalExpense = (float) $expenseQuery->sum('amount');
        $profitLoss = $totalSales - $totalPurchase - $totalExpense;

        $invoiceStatusSummary = collect(['Paid', 'Pending', 'Overdue'])
            ->map(function (string $status) use ($fromDate, $toDate) {
                $sales = SalesInvoice::query()->where('status', $status);
                $purchases = PurchaseInvoice::query()->where('status', $status);

                $this->applyDateFilter($sales, 'invoice_date', $fromDate, $toDate);
                $this->applyDateFilter($purchases, 'invoice_date', $fromDate, $toDate);

                return [
                    'status' => $status,
                    'sales_count' => $sales->count(),
                    'purchase_count' => $purchases->count(),
                ];
            });

        $budgetSummary = Budget::orderBy('name')
            ->get()
            ->map(function (Budget $budget) use ($fromDate, $toDate) {
                $expenses = $budget->expenses();
                $this->applyDateFilter($expenses, 'expense_date', $fromDate, $toDate);

                $spent = (float) $expenses->sum('amount');
                $amount = (float) $budget->amount;
                $remaining = max($amount - $spent, 0);
                $usage = $amount > 0 ? min(round(($spent / $amount) * 100), 100) : 0;

                return [
                    'name' => $budget->name,
                    'type' => $budget->type,
                    'amount' => $amount,
                    'spent' => $spent,
                    'remaining' => $remaining,
                    'usage' => $usage,
                ];
            });

        return view('admin.reports.index', compact(
            'fromDate',
            'toDate',
            'totalSales',
            'totalPurchase',
            'totalExpense',
            'profitLoss',
            'invoiceStatusSummary',
            'budgetSummary',
        ));
    }

    private function applyDateFilter($query, string $column, ?string $fromDate, ?string $toDate): void
    {
        $query
            ->when($fromDate, fn ($query) => $query->whereDate($column, '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->whereDate($column, '<=', $toDate));
    }
}
