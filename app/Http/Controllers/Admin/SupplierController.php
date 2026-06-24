<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    public function index(): View
    {
        return view('admin.suppliers.index');
    }

    public function data()
    {
        try {
            $suppliers = Supplier::query()->latest();

            return DataTables::of($suppliers)
                ->addIndexColumn()
                ->addColumn('action', function (Supplier $supplier) {
                    $editUrl = route('admin.suppliers.edit', $supplier);
                    $deleteUrl = route('admin.suppliers.destroy', $supplier);
                    $supplierName = e($supplier->name);
                    $csrf = csrf_token();

                    return <<<HTML
                        <a href="{$editUrl}" class="text-primary hover:text-blue-700">Edit</a>
                        <form method="POST" action="{$deleteUrl}" class="delete-supplier-form inline-block ms-3" data-supplier-name="{$supplierName}">
                            <input type="hidden" name="_token" value="{$csrf}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="text-danger hover:text-red-700">Delete</button>
                        </form>
                    HTML;
                })
                ->editColumn('email', fn (Supplier $supplier) => $supplier->email ?? '-')
                ->editColumn('phone', fn (Supplier $supplier) => $supplier->phone ?? '-')
                ->rawColumns(['action'])
                ->make(true);
        } catch (Throwable $e) {
            Log::error('Failed to load supplier datatable.', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Unable to load suppliers.',
            ], 500);
        }
    }

    public function create(): View
    {
        return view('admin.suppliers.create');
    }

    public function store(SupplierRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            Supplier::create($validated);
        } catch (Throwable $e) {
            Log::error('Failed to create supplier.', [
                'message' => $e->getMessage(),
                'data' => $validated,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Unable to create supplier. Please try again.');
        }

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier): View
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(SupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $supplier->update($validated);
        } catch (Throwable $e) {
            Log::error('Failed to update supplier.', [
                'message' => $e->getMessage(),
                'supplier_id' => $supplier->id,
                'data' => $validated,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Unable to update supplier. Please try again.');
        }

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        try {
            $supplier->delete();
        } catch (Throwable $e) {
            Log::error('Failed to delete supplier.', [
                'message' => $e->getMessage(),
                'supplier_id' => $supplier->id,
            ]);

            return redirect()
                ->route('admin.suppliers.index')
                ->with('error', 'Unable to delete supplier. Please try again.');
        }

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
