<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CRM\StorePipelineRequest;
use App\Models\Pipeline;
use App\Services\PipelineService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PipelineController extends Controller
{
    public function __construct(
        private readonly PipelineService $pipelineService,
    ) {}

    public function index(Request $request): View
    {
        $pipelines = Pipeline::withCount('stages')
            ->when($request->search, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('crm.pipelines.index', compact('pipelines'));
    }

    public function create(): View
    {
        return view('crm.pipelines.create');
    }

    public function store(StorePipelineRequest $request): RedirectResponse
    {
        $pipeline = $this->pipelineService->create($request->validated());

        return redirect()->route('pipelines.show', $pipeline)->with('success', 'Pipeline created successfully.');
    }

    public function show(Pipeline $pipeline): View
    {
        $pipeline->load('stages');

        return view('crm.pipelines.show', compact('pipeline'));
    }

    public function destroy(Pipeline $pipeline): RedirectResponse
    {
        $result = $this->pipelineService->delete($pipeline);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function addStage(Pipeline $pipeline, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'probability' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $this->pipelineService->addStage($pipeline, $validated);

        return back()->with('success', 'Stage added successfully.');
    }

    public function removeStage(\App\Models\PipelineStage $stage): RedirectResponse
    {
        $this->pipelineService->removeStage($stage);

        return back()->with('success', 'Stage removed successfully.');
    }
}
