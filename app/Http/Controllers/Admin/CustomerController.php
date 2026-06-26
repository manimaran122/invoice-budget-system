<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
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
                        <div class="action-buttons">
                        <a href="{$editUrl}" class="action-icon action-edit" title="Edit" aria-label="Edit customer">&#9998;</a>
                        <form method="POST" action="{$deleteUrl}" class="delete-customer-form action-form" data-customer-name="{$customerName}">
                            <input type="hidden" name="_token" value="{$csrf}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="action-icon action-delete" title="Delete" aria-label="Delete customer">&#128465;</button>
                        </form>
                        </div>
                    HTML;
                })
                ->editColumn('email', fn (Customer $customer) => $customer->email ?? '-')
                ->editColumn('phone', fn (Customer $customer) => $customer->phone ?? '-')
                ->rawColumns(['action'])
                ->make(true);
        } catch (Throwable $e) {
            LogHelper::error('Failed to load customer datatable.', $e);

            return response()->json(['message' => 'Unable to load customers.'], 500);
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
            LogHelper::error('Failed to create customer.', $e, ['data' => $validated]);

            return back()->withInput()
                ->with('error', 'Unable to create customer. Please try again.');
        }

        return back()->with('success', 'Customer created successfully.');
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
            LogHelper::error('Failed to update customer.', $e, ['customer_id' => $customer->id, 'data' => $validated]);

            return back()->withInput()
                ->with('error', 'Unable to update customer. Please try again.');
        }

        return back()->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        try {
            $customer->delete();
        } catch (Throwable $e) {
            LogHelper::error('Failed to delete customer.', $e, ['customer_id' => $customer->id]);

            return back()->with('error', 'Unable to delete customer. Please try again.');
        }

        return back()->with('success', 'Customer deleted successfully.');
    }
}
