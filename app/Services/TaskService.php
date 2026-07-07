<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Task;
use App\Support\BaseService;

class TaskService extends BaseService
{
    public function create(array $data): Task
    {
        return Task::create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task->fresh();
    }

    public function delete(Task $task): array
    {
        $task->delete();

        return ['success' => true, 'message' => 'Task deleted successfully.'];
    }

    public function complete(Task $task): Task
    {
        $task->complete();

        return $task->fresh();
    }
}
