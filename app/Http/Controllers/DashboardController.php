<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Dashboard;
use App\Services\AnalyticsService;
use App\Services\DashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
        private readonly AnalyticsService $analyticsService,
    ) {}

    public function index(Request $request): View
    {
        $dashboards = Dashboard::where('company_id', $request->user()->company_id ?? 1)
            ->withCount('widgets')
            ->latest()
            ->paginate(15);

        return view('reporting.dashboards.index', compact('dashboards'));
    }

    public function show(Dashboard $dashboard): View
    {
        $dashboard->load('widgets');

        return view('reporting.dashboards.show', compact('dashboard'));
    }

    public function create(): View
    {
        return view('reporting.dashboards.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['company_id'] = $request->user()->company_id ?? 1;
        $validated['created_by'] = $request->user()->id;

        $this->dashboardService->create($validated);

        return redirect()->route('dashboards.index')->with('success', 'Dashboard created successfully.');
    }

    public function destroy(Dashboard $dashboard): RedirectResponse
    {
        $this->dashboardService->delete($dashboard);

        return back()->with('success', 'Dashboard deleted successfully.');
    }

    public function executive(Request $request): View
    {
        $metrics = $this->analyticsService->getExecutiveMetrics(
            $request->user()->company_id ?? 1,
            $request->start_date,
            $request->end_date,
        );

        return view('reporting.executive', compact('metrics'));
    }
}
