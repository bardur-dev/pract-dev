<?php

namespace App\Http\Controllers;

use App\DTO\TaskFilterDTO;
use App\Enums\TaskStatus;
use App\Exports\TaskExport;
use App\Http\Requests\StoreOrCreateRequests;
use App\Http\Requests\TaskFilterRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Jobs\SendTaskExportEmail;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests;

    protected $perPage = 4;

    public function index(TaskFilterRequest $request)
    {
        $this->authorize('viewAny', Task::class);

        $filterDTO = TaskFilterDTO::fromArray($request->validated());

        $tasks = Task::filter($filterDTO)
            ->latest()
            ->paginate($this->perPage);

        return view('tasks.index', [
            'tasks' => $tasks,
            'taskToEdit' => null,
        ]);
    }

    public function export()
    {
        $this->authorize('viewAny', Task::class);

        SendTaskExportEmail::dispatch(Auth::user());

        return back()->with('success', 'Выгрузка начата. Файл будет отправлен на вашу почту.');
    }

    public function create()
    {
        $this->authorize('create', Task::class);

        return view('tasks.create');
    }

    public function store(StoreOrCreateRequests $request, User $user)
    {
        $this->authorize('create', Task::class);

        $validated = $request->validated();

        $request->user()->tasks()->create([
            'name' => $validated['name'],
            'status' => TaskStatus::from($validated['status']),
        ]);

        return redirect()->route('tasks.index')
            ->with('success', 'Задача добавлена');
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);

        return view('tasks.edit', compact('task'));
    }

    public function update(StoreOrCreateRequests $request, Task $task)
    {
        $this->authorize('update', $task);

        $task->update($request->validated());

        return redirect()->route('tasks.index')
            ->with('success', 'Задача обновлена');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Задача удалена');
    }

    public function toggleStatus(UpdateTaskStatusRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $task->update([
            'status' => TaskStatus::from($request->status)
        ]);

        return back()->with('success', 'Статус задачи обновлен');
    }
}
