<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\Expense;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalSales = (float) SalesInvoice::sum('total');
        $totalPurchase = (float) PurchaseInvoice::sum('total');
        $totalExpenses = (float) Expense::sum('amount');
        $remainingBudget = Budget::all()->sum(fn (Budget $budget) => $budget->remaining());

        $pendingInvoices = SalesInvoice::where('status', 'Pending')->count()
            + PurchaseInvoice::where('status', 'Pending')->count();

        $paidInvoices = SalesInvoice::where('status', 'Paid')->count()
            + PurchaseInvoice::where('status', 'Paid')->count();

        $overdueInvoices = SalesInvoice::where('status', 'Overdue')->count()
            + PurchaseInvoice::where('status', 'Overdue')->count();

        return view('admin.dashboard', compact(
            'totalSales',
            'totalPurchase',
            'totalExpenses',
            'remainingBudget',
            'pendingInvoices',
            'paidInvoices',
            'overdueInvoices',
        ));
    }
}
