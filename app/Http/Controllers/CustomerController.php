<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Customer\StoreCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerService $customerService,
    ) {}

    public function index(Request $request): View
    {
        $customers = Customer::withCount('salesOrders')
            ->when($request->search, fn ($query, $search) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('sales.customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('sales.customers.create');
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $customer = $this->customerService->create($request->validated(), $request->user());

        return redirect()->route('customers.show', $customer)->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer): View
    {
        return view('sales.customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        return view('sales.customers.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer = $this->customerService->update($customer, $request->validated());

        return redirect()->route('customers.show', $customer)->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $result = $this->customerService->delete($customer);

        return back()->with('success', $result['message']);
    }
}
