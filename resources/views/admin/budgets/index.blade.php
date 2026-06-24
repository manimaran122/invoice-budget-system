<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.min.css') }}">
    @endpush

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-app-dark leading-tight">
                Budgets
            </h2>

            <a href="{{ route('admin.budgets.create') }}" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                + Add Budget
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-app-card border border-app-border overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table id="budgets-table" class="min-w-full divide-y divide-app-border">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">Budget Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">Spent</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">Remaining</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">Usage %</th>
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
                $('#budgets-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('admin.budgets.data') }}',
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'name', name: 'name' },
                        { data: 'type', name: 'type' },
                        { data: 'amount', name: 'amount' },
                        { data: 'spent', name: 'spent' },
                        { data: 'remaining', name: 'remaining', orderable: false, searchable: false },
                        { data: 'usage', name: 'usage', orderable: false, searchable: false },
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

                $(document).on('submit', '.delete-budget-form', function (event) {
                    event.preventDefault();

                    const form = this;
                    const budgetName = $(form).data('budget-name');

                    Swal.fire({
                        title: 'Delete budget?',
                        text: `Are you sure you want to delete ${budgetName}?`,
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
