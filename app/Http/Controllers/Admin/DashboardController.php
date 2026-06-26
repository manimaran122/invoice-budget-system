<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InvoiceStatus;
use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use Illuminate\View\View;
use Throwable;

class DashboardController extends Controller
{
    // Build the admin finance summary for dashboard cards and tables.
    public function index(): View
    {
        try {
            $totalSales = SalesInvoice::totalAmount();
            $totalPurchase = PurchaseInvoice::totalAmount();
            $totalExpenses = Expense::totalAmount();
            $remainingBudget = Budget::totalRemaining();
            $profitLoss = $totalSales - $totalPurchase - $totalExpenses;

            $salesPayments = (float) Payment::sales()->sum('amount');
            $purchasePayments = (float) Payment::purchase()->sum('amount');
            $outstandingSales = max($totalSales - $salesPayments, 0);

            $pendingInvoices = SalesInvoice::countByStatus(InvoiceStatus::Pending->value)
                + PurchaseInvoice::countByStatus(InvoiceStatus::Pending->value);

            $paidInvoices = SalesInvoice::countByStatus(InvoiceStatus::Paid->value)
                + PurchaseInvoice::countByStatus(InvoiceStatus::Paid->value);

            $overdueInvoices = SalesInvoice::countByStatus(InvoiceStatus::Overdue->value)
                + PurchaseInvoice::countByStatus(InvoiceStatus::Overdue->value);

            $recentSalesInvoices = SalesInvoice::recentList();
            $recentPurchaseInvoices = PurchaseInvoice::with('supplier')
                ->latest()
                ->limit(5)
                ->get();
            $recentExpenses = Expense::recentList();

            $budgetUsages = Budget::orderBy('name')
                ->limit(5)
                ->get();

            $totalInvoiceCount = max($paidInvoices + $pendingInvoices + $overdueInvoices, 1);
            $collectionRate = round(($paidInvoices / $totalInvoiceCount) * 100);

            $invoiceStatusSummary = [
                InvoiceStatus::Paid->value => $paidInvoices,
                InvoiceStatus::Pending->value => $pendingInvoices,
                InvoiceStatus::Overdue->value => $overdueInvoices,
            ];
        } catch (Throwable $e) {
            LogHelper::error('Failed to load admin dashboard.', $e);
            abort(500, 'Unable to load dashboard.');
        }

        return view('admin.dashboard', compact('totalSales', 'totalPurchase', 'totalExpenses', 'remainingBudget', 'profitLoss', 'salesPayments', 'purchasePayments', 'outstandingSales', 'pendingInvoices', 'paidInvoices', 'overdueInvoices', 'collectionRate', 'recentSalesInvoices', 'recentPurchaseInvoices', 'recentExpenses', 'budgetUsages', 'invoiceStatusSummary'));
    }
}
