<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExpenseRequest;
use App\Models\Budget;
use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ExpenseController extends Controller
{
    public function index(): View
    {
        return view('admin.expenses.index');
    }

    public function data()
    {
        $expenses = Expense::query()
            ->with('budget')
            ->latest();

        return DataTables::of($expenses)
            ->addIndexColumn()
            ->addColumn('budget_name', fn (Expense $expense) => $expense->budget?->name ?? '-')
            ->editColumn('amount', fn (Expense $expense) => number_format($expense->amount, 2))
            ->editColumn('expense_date', fn (Expense $expense) => $expense->expense_date->format('Y-m-d'))
            ->addColumn('action', function (Expense $expense) {
                $editUrl = route('admin.expenses.edit', $expense);
                $deleteUrl = route('admin.expenses.destroy', $expense);
                $expenseTitle = e($expense->title);
                $csrf = csrf_token();

                return <<<HTML
                    <a href="{$editUrl}" class="text-primary hover:text-blue-700">Edit</a>
                    <form method="POST" action="{$deleteUrl}" class="delete-expense-form inline-block ms-3" data-expense-title="{$expenseTitle}">
                        <input type="hidden" name="_token" value="{$csrf}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="text-danger hover:text-red-700">Delete</button>
                    </form>
                HTML;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create(): View
    {
        $budgets = Budget::orderBy('name')->get();

        return view('admin.expenses.create', compact('budgets'));
    }

    public function store(ExpenseRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $warning = false;

        DB::transaction(function () use ($validated, &$warning) {
            $budget = Budget::lockForUpdate()->findOrFail($validated['budget_id']);
            $newSpent = (float) $budget->spent + (float) $validated['amount'];
            $warning = $newSpent > (float) $budget->amount;

            Expense::create($validated);
            $budget->update(['spent' => $newSpent]);
        });

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', 'Expense created successfully.')
            ->with('warning', $warning ? 'Expense exceeded budget limit.' : null);
    }

    public function edit(Expense $expense): View
    {
        $budgets = Budget::orderBy('name')->get();

        return view('admin.expenses.edit', compact('expense', 'budgets'));
    }

    public function update(ExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $validated = $request->validated();
        $warning = false;

        DB::transaction(function () use ($validated, $expense, &$warning) {
            $oldBudget = Budget::lockForUpdate()->findOrFail($expense->budget_id);
            $oldBudget->update([
                'spent' => max((float) $oldBudget->spent - (float) $expense->amount, 0),
            ]);

            $newBudget = Budget::lockForUpdate()->findOrFail($validated['budget_id']);
            $newSpent = (float) $newBudget->spent + (float) $validated['amount'];
            $warning = $newSpent > (float) $newBudget->amount;

            $expense->update($validated);
            $newBudget->update(['spent' => $newSpent]);
        });

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', 'Expense updated successfully.')
            ->with('warning', $warning ? 'Expense exceeded budget limit.' : null);
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        DB::transaction(function () use ($expense) {
            $budget = Budget::lockForUpdate()->findOrFail($expense->budget_id);
            $budget->update([
                'spent' => max((float) $budget->spent - (float) $expense->amount, 0),
            ]);

            $expense->delete();
        });

        return redirect()
            ->route('admin.expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }
}
