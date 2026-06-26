<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-app-dark leading-tight">
            Edit Product/Service
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-app-card border border-app-border overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('admin.product-services.update', $productService) }}" class="p-6 space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label for="name" value="Name" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $productService->name) }}" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="type" value="Type" />
                        <select id="type" name="type" class="mt-1 block w-full rounded-md border-app-border shadow-sm focus:border-primary focus:ring-primary" required>
                            <option value="">Select type</option>
                            @foreach (\App\Enums\ProductServiceType::cases() as $type)
                                <option value="{{ $type->value }}" @selected(old('type', $productService->type) === $type->value)>{{ $type->value }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <x-input-label for="price" value="Price" />
                            <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 block w-full" value="{{ old('price', $productService->price) }}" required />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="tax_percentage" value="Tax %" />
                            <x-text-input id="tax_percentage" name="tax_percentage" type="number" step="0.01" min="0" max="100" class="mt-1 block w-full" value="{{ old('tax_percentage', $productService->tax_percentage) }}" required />
                            <x-input-error :messages="$errors->get('tax_percentage')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.product-services.index') }}" class="text-sm text-app-muted hover:text-app-dark">Cancel</a>
                        <x-primary-button>Update</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
