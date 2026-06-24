<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.min.css') }}">
    @endpush

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-app-dark leading-tight">
                Expenses
            </h2>

            <a href="{{ route('admin.expenses.create') }}" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                + Add Expense
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('warning'))
                <div class="mb-6 rounded-md border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm font-medium text-warning">
                    Warning: {{ session('warning') }}
                </div>
            @endif

            <div class="bg-app-card border border-app-border overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table id="expenses-table" class="min-w-full divide-y divide-app-border">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">Expense Title</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">Budget</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">Category</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">Date</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-app-muted uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-app-border"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('vendor/datatables/dataTables.min.js') }}"></script>
        <script>
            $(document).ready(function () {
                $('#expenses-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('admin.expenses.data') }}',
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'title', name: 'title' },
                        { data: 'budget_name', name: 'budget.name' },
                        { data: 'category', name: 'category' },
                        { data: 'amount', name: 'amount' },
                        { data: 'expense_date', name: 'expense_date' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ]
                });

                @if (session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: @json(session('success')),
                        timer: 2000,
                        showConfirmButton: false
                    });
                @endif

                @if (session('warning'))
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: @json(session('warning')),
                        confirmButtonColor: '#F59E0B'
                    });
                @endif

                $(document).on('submit', '.delete-expense-form', function (event) {
                    event.preventDefault();

                    const form = this;
                    const expenseTitle = $(form).data('expense-title');

                    Swal.fire({
                        title: 'Delete expense?',
                        text: `Are you sure you want to delete ${expenseTitle}?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#DC2626',
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: 'Yes, delete it'
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
