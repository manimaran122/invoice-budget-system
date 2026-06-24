<x-app-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.min.css') }}">
    @endpush

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-app-dark leading-tight">
                Products & Services
            </h2>

            <a href="{{ route('admin.product-services.create') }}" class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                + Add Product/Service
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-app-card border border-app-border overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table id="product-services-table" class="min-w-full divide-y divide-app-border">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-app-muted uppercase">Tax %</th>
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
                $('#product-services-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('admin.product-services.data') }}',
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'name', name: 'name' },
                        { data: 'type', name: 'type' },
                        { data: 'price', name: 'price' },
                        { data: 'tax_percentage', name: 'tax_percentage' },
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

                $(document).on('submit', '.delete-product-service-form', function (event) {
                    event.preventDefault();

                    const form = this;
                    const productServiceName = $(form).data('product-service-name');

                    Swal.fire({
                        title: 'Delete product/service?',
                        text: `Are you sure you want to delete ${productServiceName}?`,
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
