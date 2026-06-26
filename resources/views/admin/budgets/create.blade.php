<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-app-dark leading-tight">
            Add Budget
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-app-card border border-app-border overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('admin.budgets.store') }}" class="p-6 space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="name" value="Budget Name" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name') }}" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="type" value="Type" />
                        <select id="type" name="type" class="mt-1 block w-full rounded-md border-app-border shadow-sm focus:border-primary focus:ring-primary" required>
                            <option value="">Select type</option>
                            @foreach (\App\Enums\BudgetType::cases() as $type)
                                <option value="{{ $type->value }}" @selected(old('type') === $type->value)>{{ $type->value }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="amount" value="Amount" />
                            <x-text-input id="amount" name="amount" type="number" step="0.01" min="0" class="budget-amount mt-1 block w-full" value="{{ old('amount', 0) }}" required />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="spent" value="Spent" />
                            <x-text-input id="spent" name="spent" type="number" step="0.01" min="0" class="budget-amount mt-1 block w-full" value="{{ old('spent', 0) }}" />
                            <x-input-error :messages="$errors->get('spent')" class="mt-2" />
                        </div>
                    </div>

                    <div class="rounded-md border border-app-border bg-app-background p-4">
                        <div class="mb-2 flex items-center justify-between text-sm">
                            <span class="font-medium text-app-dark">Budget Usage</span>
                            <span id="usage-preview" class="font-semibold text-app-dark">0%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-app-border">
                            <div id="usage-bar" class="h-full bg-success" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.budgets.index') }}" class="text-sm text-app-muted hover:text-app-dark">Cancel</a>
                        <x-primary-button>Save</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                function updateUsage() {
                    const amount = parseFloat($('#amount').val()) || 0;
                    const spent = parseFloat($('#spent').val()) || 0;
                    const usage = amount > 0 ? Math.min(Math.round((spent / amount) * 100), 100) : 0;

                    $('#usage-preview').text(`${usage}%`);
                    $('#usage-bar').css('width', `${usage}%`);
                }

                $('.budget-amount').on('input', updateUsage);
                updateUsage();
            });
        </script>
    @endpush
</x-app-layout>
