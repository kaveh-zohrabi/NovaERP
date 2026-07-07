<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CRM\StoreLeadRequest;
use App\Http\Requests\CRM\UpdateLeadRequest;
use App\Models\Lead;
use App\Services\CustomerConversionService;
use App\Services\LeadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function __construct(
        private readonly LeadService $leadService,
        private readonly CustomerConversionService $conversionService,
    ) {}

    public function index(Request $request): View
    {
        $leads = Lead::with('assignedEmployee', 'convertedCustomer')
            ->when($request->search, fn ($query, $search) => $query
                ->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('company_name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
            )
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('crm.leads.index', compact('leads'));
    }

    public function create(): View
    {
        return view('crm.leads.create');
    }

    public function store(StoreLeadRequest $request): RedirectResponse
    {
        $lead = $this->leadService->create($request->validated());

        return redirect()->route('leads.show', $lead)->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead): View
    {
        $lead->load('assignedEmployee', 'convertedCustomer', 'contacts', 'opportunities', 'activities', 'notes');

        return view('crm.leads.show', compact('lead'));
    }

    public function edit(Lead $lead): View
    {
        return view('crm.leads.edit', compact('lead'));
    }

    public function update(UpdateLeadRequest $request, Lead $lead): RedirectResponse
    {
        $this->leadService->update($lead, $request->validated());

        return redirect()->route('leads.show', $lead)->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $result = $this->leadService->delete($lead);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function convert(Lead $lead): RedirectResponse
    {
        $customer = $this->conversionService->convert($lead);

        return redirect()->route('customers.show', $customer)->with('success', 'Lead converted to customer successfully.');
    }
}
