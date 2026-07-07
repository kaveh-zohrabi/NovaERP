<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CRM\StoreActivityRequest;
use App\Models\Activity;
use App\Services\ActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function __construct(
        private readonly ActivityService $activityService,
    ) {}

    public function index(Request $request): View
    {
        $activities = Activity::with('assignedEmployee', 'subjectable')
            ->when($request->search, fn ($query, $search) => $query->where('title', 'like', "%{$search}%"))
            ->when($request->type, fn ($query, $type) => $query->where('type', $type))
            ->when($request->is_completed, fn ($query, $completed) => $query->where('is_completed', $completed))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('crm.activities.index', compact('activities'));
    }

    public function store(StoreActivityRequest $request): RedirectResponse
    {
        $this->activityService->create($request->validated());

        return back()->with('success', 'Activity created successfully.');
    }

    public function complete(Activity $activity): RedirectResponse
    {
        $this->activityService->complete($activity);

        return back()->with('success', 'Activity completed.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        $this->activityService->delete($activity);

        return back()->with('success', 'Activity deleted successfully.');
    }
}
