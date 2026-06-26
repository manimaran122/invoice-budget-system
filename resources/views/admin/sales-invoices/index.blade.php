<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.min.css') }}">
    @endpush

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-app-dark leading-tight">
                Sales Invoices
            </h2>

            <a href="{{ route('admin.sales-invoices.create') }}" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                + Create Sales Invoice
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-app-card border border-app-border overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-5 flex justify-end">
                        <div class="flex items-center gap-3">
                            <label for="status-filter" class="text-sm font-medium text-app-dark">Status</label>
                            <select id="status-filter" autocomplete="off" class="w-44 rounded-md border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary">
                                <option value="">All</option>
                                @foreach (\App\Enums\InvoiceStatus::cases() as $status)
                                    <option value="{{ $status->value }}">{{ $status->value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <table id="sales-invoices-table" class="min-w-full divide-y divide-app-border">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">Invoice No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">Customer</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">Due Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">Status</th>
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
                const table = $('#sales-invoices-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('admin.sales-invoices.data') }}',
                        data: function (data) {
                            data.status = $('#status-filter').val();
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'invoice_number', name: 'invoice_number' },
                        { data: 'customer_name', name: 'customer.name' },
                        { data: 'invoice_date', name: 'invoice_date' },
                        { data: 'due_date', name: 'due_date' },
                        { data: 'total', name: 'total' },
                        { data: 'status', name: 'status' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ]
                });

                $('#status-filter').on('change', function () {
                    table.ajax.reload();
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

                $(document).on('submit', '.delete-sales-invoice-form', function (event) {
                    event.preventDefault();

                    const form = this;
                    const invoiceNumber = $(form).data('invoice-number');

                    Swal.fire({
                        title: 'Delete sales invoice?',
                        text: `Are you sure you want to delete ${invoiceNumber}?`,
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
