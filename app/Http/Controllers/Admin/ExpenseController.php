<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExpenseRequest;
use App\Models\Budget;
use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class ExpenseController extends Controller
{
    public function index(): View
    {
        return view('admin.expenses.index');
    }

    public function data()
    {
        try {
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
                        <div class="action-buttons">
                        <a href="{$editUrl}" class="action-icon action-edit" title="Edit" aria-label="Edit expense">&#9998;</a>
                        <form method="POST" action="{$deleteUrl}" class="delete-expense-form action-form" data-expense-title="{$expenseTitle}">
                            <input type="hidden" name="_token" value="{$csrf}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="action-icon action-delete" title="Delete" aria-label="Delete expense">&#128465;</button>
                        </form>
                        </div>
                    HTML;
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (Throwable $e) {
            LogHelper::error('Failed to load expense datatable.', $e);

            return response()->json(['message' => 'Unable to load expenses.'], 500);
        }
    }

    public function create(): View
    {
        try {
            $budgets = Budget::orderBy('name')->get();
        } catch (Throwable $e) {
            LogHelper::error('Failed to load create expense page.', $e);
            abort(500, 'Unable to load expense form.');
        }

        return view('admin.expenses.create', compact('budgets'));
    }

    public function store(ExpenseRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $warning = false;

        try {
            DB::transaction(function () use ($validated, &$warning) {
                $budget = Budget::lockForUpdate()->findOrFail($validated['budget_id']);
                $newSpent = (float) $budget->spent + (float) $validated['amount'];
                $warning = $newSpent > (float) $budget->amount;

                Expense::create($validated);
                $budget->update(['spent' => $newSpent]);
            });
        } catch (Throwable $e) {
            LogHelper::error('Failed to create expense.', $e, ['data' => $validated]);

            return back()->withInput()
                ->with('error', 'Unable to create expense. Please try again.');
        }

        return back()
            ->with('success', 'Expense created successfully.')
            ->with('warning', $warning ? 'Expense exceeded budget limit.' : null);
    }

    public function edit(Expense $expense): View
    {
        try {
            $budgets = Budget::orderBy('name')->get();
        } catch (Throwable $e) {
            LogHelper::error('Failed to load edit expense page.', $e, ['expense_id' => $expense->id]);
            abort(500, 'Unable to load expense form.');
        }

        return view('admin.expenses.edit', compact('expense', 'budgets'));
    }

    public function update(ExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $validated = $request->validated();
        $warning = false;

        try {
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
        } catch (Throwable $e) {
            LogHelper::error('Failed to update expense.', $e, ['expense_id' => $expense->id, 'data' => $validated]);

            return back()->withInput()
                ->with('error', 'Unable to update expense. Please try again.');
        }

        return back()->with('success', 'Expense updated successfully.')
            ->with('warning', $warning ? 'Expense exceeded budget limit.' : null);
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        try {
            DB::transaction(function () use ($expense) {
                $budget = Budget::lockForUpdate()->findOrFail($expense->budget_id);
                $budget->update(['spent' => max((float) $budget->spent - (float) $expense->amount, 0)]);
                $expense->delete();
            });
        } catch (Throwable $e) {
            LogHelper::error('Failed to delete expense.', $e, ['expense_id' => $expense->id]);

            return back()->with('error', 'Unable to delete expense. Please try again.');
        }

        return back()->with('success', 'Expense deleted successfully.');
    }
}
