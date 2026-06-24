<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-app-dark leading-tight">
            Edit Expense
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-app-card border border-app-border overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('admin.expenses.update', $expense) }}" class="p-6 space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label for="title" value="Expense Title" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ old('title', $expense->title) }}" required autofocus />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="budget_id" value="Budget" />
                        <select id="budget_id" name="budget_id" class="mt-1 block w-full rounded-md border-app-border shadow-sm focus:border-primary focus:ring-primary" required>
                            <option value="">Select budget</option>
                            @foreach ($budgets as $budget)
                                @php
                                    $remaining = $budget->id === $expense->budget_id ? $budget->remaining() + (float) $expense->amount : $budget->remaining();
                                @endphp
                                <option value="{{ $budget->id }}" data-remaining="{{ $remaining }}" @selected(old('budget_id', $expense->budget_id) == $budget->id)>{{ $budget->name }} - Remaining {{ number_format($remaining, 2) }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('budget_id')" class="mt-2" />
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="category" value="Category" />
                            <x-text-input id="category" name="category" type="text" class="mt-1 block w-full" value="{{ old('category', $expense->category) }}" required />
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="expense_date" value="Date" />
                            <x-text-input id="expense_date" name="expense_date" type="date" class="mt-1 block w-full" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required />
                            <x-input-error :messages="$errors->get('expense_date')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="amount" value="Amount" />
                        <x-text-input id="amount" name="amount" type="number" step="0.01" min="0" class="mt-1 block w-full" value="{{ old('amount', $expense->amount) }}" required />
                        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                    </div>

                    <div id="budget-warning" class="hidden rounded-md border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm font-medium text-warning">
                        Warning: Expense exceeded budget limit
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.expenses.index') }}" class="text-sm text-app-muted hover:text-app-dark">Cancel</a>
                        <x-primary-button>Update</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                function checkBudgetLimit() {
                    const remaining = parseFloat($('#budget_id option:selected').data('remaining')) || 0;
                    const amount = parseFloat($('#amount').val()) || 0;

                    $('#budget-warning').toggleClass('hidden', ! $('#budget_id').val() || amount <= remaining);
                }

                $('#budget_id, #amount').on('input change', checkBudgetLimit);
                checkBudgetLimit();
            });
        </script>
    @endpush
</x-app-layout>
