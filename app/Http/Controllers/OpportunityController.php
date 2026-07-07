<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CRM\StoreOpportunityRequest;
use App\Http\Requests\CRM\UpdateOpportunityRequest;
use App\Models\Opportunity;
use App\Services\OpportunityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OpportunityController extends Controller
{
    public function __construct(
        private readonly OpportunityService $opportunityService,
    ) {}

    public function index(Request $request): View
    {
        $opportunities = Opportunity::with('customer', 'lead', 'pipeline', 'pipelineStage', 'assignedEmployee')
            ->when($request->search, fn ($query, $search) => $query
                ->where('title', 'like', "%{$search}%")
                ->orWhereHas('customer', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            )
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->when($request->pipeline_id, fn ($query, $id) => $query->where('pipeline_id', $id))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('crm.opportunities.index', compact('opportunities'));
    }

    public function create(): View
    {
        return view('crm.opportunities.create');
    }

    public function store(StoreOpportunityRequest $request): RedirectResponse
    {
        $opportunity = $this->opportunityService->create($request->validated());

        return redirect()->route('opportunities.show', $opportunity)->with('success', 'Opportunity created successfully.');
    }

    public function show(Opportunity $opportunity): View
    {
        $opportunity->load('customer', 'lead', 'pipeline', 'pipelineStage', 'assignedEmployee', 'activities', 'notes', 'tasks');

        return view('crm.opportunities.show', compact('opportunity'));
    }

    public function edit(Opportunity $opportunity): View
    {
        return view('crm.opportunities.edit', compact('opportunity'));
    }

    public function update(UpdateOpportunityRequest $request, Opportunity $opportunity): RedirectResponse
    {
        $this->opportunityService->update($opportunity, $request->validated());

        return redirect()->route('opportunities.show', $opportunity)->with('success', 'Opportunity updated successfully.');
    }

    public function destroy(Opportunity $opportunity): RedirectResponse
    {
        $result = $this->opportunityService->delete($opportunity);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function markWon(Opportunity $opportunity): RedirectResponse
    {
        $this->opportunityService->markWon($opportunity);

        return back()->with('success', 'Opportunity marked as won.');
    }

    public function markLost(Opportunity $opportunity): RedirectResponse
    {
        $reason = request('reason', '');

        $this->opportunityService->markLost($opportunity, $reason);

        return back()->with('success', 'Opportunity marked as lost.');
    }
}
