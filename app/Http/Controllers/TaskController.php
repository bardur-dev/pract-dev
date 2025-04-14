<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Http\Requests\StoreOrCreateRequests;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests;
    protected $perPage = 4;
    public function index()
    {
        $tasks = auth()->user()->tasks()->latest()->paginate($this->perPage);

        return view('tasks.index', [
            'tasks' => $tasks,
            'taskToEdit' => null,
        ]);
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(StoreOrCreateRequests $request, User $user)
    {
        $request->user()->tasks()->create([
            'name' => $request->validated()['name'],
            'status' => 'pending',
        ]);

        return redirect()->route('tasks.index')
            ->with('success', 'Задача добавлена');
    }

    public function edit(Task $task)
    {
        return view('tasks.edit', compact('task'));
    }

    public function update(StoreOrCreateRequests $request, Task $task)
    {
        $task->update($request->validated());

        return redirect()->route('tasks.index')
            ->with('success', 'Задача обновлена');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Задача удалена');
    }

    public function toggleStatus(UpdateTaskStatusRequest $request, Task $task)
    {

        $task->update([
            'status' => TaskStatus::from($request->status)
        ]);

        return back()->with('success', 'Статус задачи обновлен');
    }
}
