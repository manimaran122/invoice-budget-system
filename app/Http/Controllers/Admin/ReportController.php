<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InvoiceStatus;
use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use App\Models\Budget;
use App\Models\Expense;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use Illuminate\View\View;
use Throwable;

class ReportController extends Controller
{
    public function index(ReportRequest $request): View
    {
        $validated = $request->validated();

        $fromDate = $validated['from_date'] ?? null;
        $toDate = $validated['to_date'] ?? null;

        try {
            $totalSales = (float) SalesInvoice::betweenInvoiceDates($fromDate, $toDate)->sum('total');
            $totalPurchase = (float) PurchaseInvoice::betweenInvoiceDates($fromDate, $toDate)->sum('total');
            $totalExpense = (float) Expense::betweenExpenseDates($fromDate, $toDate)->sum('amount');
            $profitLoss = $totalSales - $totalPurchase - $totalExpense;

            $invoiceStatusSummary = collect(InvoiceStatus::cases())
                ->map(function (InvoiceStatus $status) use ($fromDate, $toDate) {
                    $sales = SalesInvoice::status($status->value)->betweenInvoiceDates($fromDate, $toDate);
                    $purchases = PurchaseInvoice::status($status->value)->betweenInvoiceDates($fromDate, $toDate);

                    return [
                        'status' => $status->value,
                        'sales_count' => $sales->count(),
                        'purchase_count' => $purchases->count(),
                    ];
                });

            $budgetSummary = Budget::query()
                ->withSum([
                    'expenses as filtered_spent' => fn ($query) => $query->betweenExpenseDates($fromDate, $toDate),
                ], 'amount')
                ->orderBy('name')
                ->get()
                ->map(function (Budget $budget) {
                    $spent = (float) ($budget->filtered_spent ?? 0);
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
        } catch (Throwable $e) {
            LogHelper::error('Failed to load reports page.', $e, ['from_date' => $fromDate, 'to_date' => $toDate]);
            abort(500, 'Unable to load reports.');
        }

        return view('admin.reports.index', compact('fromDate', 'toDate', 'totalSales', 'totalPurchase', 'totalExpense', 'profitLoss', 'invoiceStatusSummary', 'budgetSummary'));
    }
}
