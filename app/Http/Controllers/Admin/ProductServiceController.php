<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProductServiceType;
use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductServiceRequest;
use App\Models\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class ProductServiceController extends Controller
{
    public function index(): View
    {
        return view('admin.product-services.index');
    }

    public function data()
    {
        try {
            $productServices = ProductService::query()->latest();

            return DataTables::of($productServices)
                ->addIndexColumn()
                ->editColumn('type', function (ProductService $productService) {
                    $class = ProductServiceType::badgeClassFor($productService->type);

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
                        <div class="action-buttons">
                        <a href="{$editUrl}" class="action-icon action-edit" title="Edit" aria-label="Edit product/service">&#9998;</a>
                        <form method="POST" action="{$deleteUrl}" class="delete-product-service-form action-form" data-product-service-name="{$name}">
                            <input type="hidden" name="_token" value="{$csrf}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="action-icon action-delete" title="Delete" aria-label="Delete product/service">&#128465;</button>
                        </form>
                        </div>
                    HTML;
                })
                ->rawColumns(['type', 'action'])
                ->make(true);
        } catch (Throwable $e) {
            LogHelper::error('Failed to load product/service datatable.', $e);

            return response()->json(['message' => 'Unable to load products and services.'], 500);
        }
    }

    public function create(): View
    {
        return view('admin.product-services.create');
    }

    public function store(ProductServiceRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        try {
            ProductService::create($validated);
        } catch (Throwable $e) {
            LogHelper::error('Failed to create product/service.', $e, ['data' => $validated]);

            return back()->withInput()
                ->with('error', 'Unable to create product/service. Please try again.');
        }

        return back()->with('success', 'Product/Service created successfully.');
    }

    public function edit(ProductService $productService): View
    {
        return view('admin.product-services.edit', compact('productService'));
    }

    public function update(ProductServiceRequest $request, ProductService $productService): RedirectResponse
    {
        $validated = $request->validated();
        try {
            $productService->update($validated);
        } catch (Throwable $e) {
            LogHelper::error('Failed to update product/service.', $e, ['product_service_id' => $productService->id, 'data' => $validated]);

            return back()->withInput()
                ->with('error', 'Unable to update product/service. Please try again.');
        }

        return back()->with('success', 'Product/Service updated successfully.');
    }

    public function destroy(ProductService $productService): RedirectResponse
    {
        try {
            $productService->delete();
        } catch (Throwable $e) {
            LogHelper::error('Failed to delete product/service.', $e, ['product_service_id' => $productService->id]);

            return back()->with('error', 'Unable to delete product/service. Please try again.');
        }

        return back()->with('success', 'Product/Service deleted successfully.');
    }
}
