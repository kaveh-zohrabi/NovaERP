<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CRM\StoreTaskRequest;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService,
    ) {}

    public function index(Request $request): View
    {
        $tasks = Task::with('assignedEmployee', 'creator', 'taskable')
            ->when($request->search, fn ($query, $search) => $query->where('title', 'like', "%{$search}%"))
            ->when($request->priority, fn ($query, $priority) => $query->where('priority', $priority))
            ->when($request->is_completed !== null, fn ($query) => $query->where('is_completed', $request->boolean('is_completed')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('crm.tasks.index', compact('tasks'));
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        $this->taskService->create($data);

        return back()->with('success', 'Task created successfully.');
    }

    public function complete(Task $task): RedirectResponse
    {
        $this->taskService->complete($task);

        return back()->with('success', 'Task completed.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $this->taskService->delete($task);

        return back()->with('success', 'Task deleted successfully.');
    }
}
