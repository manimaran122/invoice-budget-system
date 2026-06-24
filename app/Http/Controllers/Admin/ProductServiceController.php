<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductServiceRequest;
use App\Models\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ProductServiceController extends Controller
{
    public function index(): View
    {
        return view('admin.product-services.index');
    }

    public function data()
    {
        $productServices = ProductService::query()->latest();

        return DataTables::of($productServices)
            ->addIndexColumn()
            ->editColumn('type', function (ProductService $productService) {
                $class = $productService->type === 'Product'
                    ? 'bg-blue-100 text-primary'
                    : 'bg-green-100 text-success';

                return '<span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold '.$class.'">'.$productService->type.'</span>';
            })
            ->editColumn('price', fn (ProductService $productService) => number_format($productService->price, 2))
            ->editColumn('tax_percentage', fn (ProductService $productService) => number_format($productService->tax_percentage, 2).'%')
            ->addColumn('action', function (ProductService $productService) {
                $editUrl = route('admin.product-services.edit', $productService);
                $deleteUrl = route('admin.product-services.destroy', $productService);
                $name = e($productService->name);
                $csrf = csrf_token();

                return <<<HTML
                    <a href="{$editUrl}" class="text-primary hover:text-blue-700">Edit</a>
                    <form method="POST" action="{$deleteUrl}" class="delete-product-service-form inline-block ms-3" data-product-service-name="{$name}">
                        <input type="hidden" name="_token" value="{$csrf}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="text-danger hover:text-red-700">Delete</button>
                    </form>
                HTML;
            })
            ->rawColumns(['type', 'action'])
            ->make(true);
    }

    public function create(): View
    {
        return view('admin.product-services.create');
    }

    public function store(ProductServiceRequest $request): RedirectResponse
    {
        ProductService::create($request->validated());

        return redirect()
            ->route('admin.product-services.index')
            ->with('success', 'Product/Service created successfully.');
    }

    public function edit(ProductService $productService): View
    {
        return view('admin.product-services.edit', compact('productService'));
    }

    public function update(ProductServiceRequest $request, ProductService $productService): RedirectResponse
    {
        $productService->update($request->validated());

        return redirect()
            ->route('admin.product-services.index')
            ->with('success', 'Product/Service updated successfully.');
    }

    public function destroy(ProductService $productService): RedirectResponse
    {
        $productService->delete();

        return redirect()
            ->route('admin.product-services.index')
            ->with('success', 'Product/Service deleted successfully.');
    }
}
