<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function index(): View
    {
        return view('admin.customers.index');
    }

    public function data()
    {
        try {
            $customers = Customer::query()->latest();

            return DataTables::of($customers)
                ->addIndexColumn()
                ->addColumn('action', function (Customer $customer) {
                    $editUrl = route('admin.customers.edit', $customer);
                    $deleteUrl = route('admin.customers.destroy', $customer);
                    $customerName = e($customer->name);
                    $csrf = csrf_token();

                    return <<<HTML
                        <a href="{$editUrl}" class="text-primary hover:text-blue-700">Edit</a>
                        <form method="POST" action="{$deleteUrl}" class="delete-customer-form inline-block ms-3" data-customer-name="{$customerName}">
                            <input type="hidden" name="_token" value="{$csrf}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="text-danger hover:text-red-700">Delete</button>
                        </form>
                    HTML;
                })
                ->editColumn('email', fn (Customer $customer) => $customer->email ?? '-')
                ->editColumn('phone', fn (Customer $customer) => $customer->phone ?? '-')
                ->rawColumns(['action'])
                ->make(true);
        } catch (Throwable $e) {
            Log::error('Failed to load customer datatable.', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Unable to load customers.',
            ], 500);
        }
    }

    public function create(): View
    {
        return view('admin.customers.create');
    }

    public function store(CustomerRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            Customer::create($validated);
        } catch (Throwable $e) {
            Log::error('Failed to create customer.', [
                'message' => $e->getMessage(),
                'data' => $validated,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Unable to create customer. Please try again.');
        }

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer): View
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(CustomerRequest $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $customer->update($validated);
        } catch (Throwable $e) {
            Log::error('Failed to update customer.', [
                'message' => $e->getMessage(),
                'customer_id' => $customer->id,
                'data' => $validated,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Unable to update customer. Please try again.');
        }

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        try {
            $customer->delete();
        } catch (Throwable $e) {
            Log::error('Failed to delete customer.', [
                'message' => $e->getMessage(),
                'customer_id' => $customer->id,
            ]);

            return redirect()
                ->route('admin.customers.index')
                ->with('error', 'Unable to delete customer. Please try again.');
        }

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
