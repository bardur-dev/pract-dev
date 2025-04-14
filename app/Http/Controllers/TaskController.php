<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrCreateRequests;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $tasks = Auth::user()->tasks()->latest()->get();
        return view('tasks.index', compact('tasks'));
    }

    public function store(StoreOrCreateRequests $request)
    {
        Task::create([
            'name' => $request->validated()['name'],
            'user_id' => Auth::id(),
            'status' => 'pending'
        ]);

        return redirect()->route('tasks.index')
            ->with('success', 'Задача добавлена');
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

    public function toggleStatus(Request $request, Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            return back()->withErrors('У вас нет прав для изменения этой задачи.');
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);


        $task->update(['status' => $request->status]);

        return back()->with('success', 'Статус задачи обновлен');
    }
}
