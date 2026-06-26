<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BudgetType;
use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\BudgetRequest;
use App\Models\Budget;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class BudgetController extends Controller
{
    public function index(): View
    {
        return view('admin.budgets.index');
    }

    public function data()
    {
        try {
            $budgets = Budget::query()->latest();

            return DataTables::of($budgets)->addIndexColumn()
                ->editColumn('type', function (Budget $budget) {
                    $class = BudgetType::badgeClassFor($budget->type);

                    return '<span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold '.$class.'">'.$budget->type.'</span>';
                })
                ->editColumn('amount', fn (Budget $budget) => number_format($budget->amount, 2))
                ->editColumn('spent', fn (Budget $budget) => number_format($budget->spent, 2))
                ->addColumn('remaining', fn (Budget $budget) => number_format($budget->remaining(), 2))
                ->addColumn('usage', function (Budget $budget) {
                    $usage = round($budget->usagePercentage());
                    $barClass = $usage >= 90 ? 'bg-danger' : ($usage >= 70 ? 'bg-warning' : 'bg-success');

                    return <<<HTML
                        <div class="flex min-w-40 items-center gap-3">
                            <div class="h-2 w-28 overflow-hidden rounded-full bg-app-border">
                                <div class="h-full {$barClass}" style="width: {$usage}%"></div>
                            </div>
                            <span class="text-sm font-semibold text-app-dark">{$usage}%</span>
                        </div>
                    HTML;
                })
                ->addColumn('action', function (Budget $budget) {
                    $editUrl = route('admin.budgets.edit', $budget);
                    $deleteUrl = route('admin.budgets.destroy', $budget);
                    $budgetName = e($budget->name);
                    $csrf = csrf_token();

                    return <<<HTML
                        <div class="action-buttons">
                        <a href="{$editUrl}" class="action-icon action-edit" title="Edit" aria-label="Edit budget">&#9998;</a>
                        <form method="POST" action="{$deleteUrl}" class="delete-budget-form action-form" data-budget-name="{$budgetName}">
                            <input type="hidden" name="_token" value="{$csrf}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="action-icon action-delete" title="Delete" aria-label="Delete budget">&#128465;</button>
                        </form>
                        </div>
                    HTML;
                })
                ->rawColumns(['type', 'usage', 'action'])->make(true);
        } catch (Throwable $e) {
            LogHelper::error('Failed to load budget datatable.', $e);

            return response()->json(['message' => 'Unable to load budgets.'], 500);
        }
    }

    public function create(): View
    {
        return view('admin.budgets.create');
    }

    public function store(BudgetRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['spent'] = $validated['spent'] ?? 0;

        try {
            Budget::create($validated);
        } catch (Throwable $e) {
            LogHelper::error('Failed to create budget.', $e, ['data' => $validated]);

            return back()->withInput()
                ->with('error', 'Unable to create budget. Please try again.');
        }

        return back()->with('success', 'Budget created successfully.');
    }

    public function edit(Budget $budget): View
    {
        return view('admin.budgets.edit', compact('budget'));
    }

    public function update(BudgetRequest $request, Budget $budget): RedirectResponse
    {
        $validated = $request->validated();
        $validated['spent'] = $validated['spent'] ?? 0;

        try {
            $budget->update($validated);
        } catch (Throwable $e) {
            LogHelper::error('Failed to update budget.', $e, ['budget_id' => $budget->id, 'data' => $validated]);

            return back()->withInput()->with('error', 'Unable to update budget. Please try again.');
        }

        return back()->with('success', 'Budget updated successfully.');
    }

    public function destroy(Budget $budget): RedirectResponse
    {
        try {
            $budget->delete();
        } catch (Throwable $e) {
            LogHelper::error('Failed to delete budget.', $e, ['budget_id' => $budget->id]);

            return back()->with('error', 'Unable to delete budget. Please try again.');
        }

        return back()->with('success', 'Budget deleted successfully.');
    }
}
