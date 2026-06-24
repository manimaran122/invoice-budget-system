<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.min.css') }}">
    @endpush

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-app-dark leading-tight">
                Purchase Invoices
            </h2>

            <a href="{{ route('admin.purchase-invoices.create') }}" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                + Create Purchase Invoice
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-app-card border border-app-border overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table id="purchase-invoices-table" class="min-w-full divide-y divide-app-border">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">Invoice No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">Supplier</th>
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
                $('#purchase-invoices-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('admin.purchase-invoices.data') }}',
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'invoice_number', name: 'invoice_number' },
                        { data: 'supplier_name', name: 'supplier.name' },
                        { data: 'invoice_date', name: 'invoice_date' },
                        { data: 'due_date', name: 'due_date' },
                        { data: 'total', name: 'total' },
                        { data: 'status', name: 'status' },
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

                $(document).on('submit', '.delete-purchase-invoice-form', function (event) {
                    event.preventDefault();

                    const form = this;
                    const invoiceNumber = $(form).data('invoice-number');

                    Swal.fire({
                        title: 'Delete purchase invoice?',
                        text: `Are you sure you want to delete ${invoiceNumber}?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#DC2626',
                        cancelButtonColor: '#64748B',
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
