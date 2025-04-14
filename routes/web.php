<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index'])
            ->middleware('can:viewAny,App\Models\Task')
            ->name('tasks.index');

        Route::get('/create', [TaskController::class, 'create'])
            ->middleware('can:create,App\Models\Task')
            ->name('tasks.create');

        Route::post('/', [TaskController::class, 'store'])
            ->middleware('can:create,App\Models\Task')
            ->name('tasks.store');

        Route::get('/{task}/edit', [TaskController::class, 'edit'])
            ->middleware('can:update,task')
            ->name('tasks.edit');

        Route::put('/{task}', [TaskController::class, 'update'])
            ->middleware('can:update,task')
            ->name('tasks.update');

        Route::delete('/{task}', [TaskController::class, 'destroy'])
            ->middleware('can:delete,task')
            ->name('tasks.destroy');

        Route::post('/{task}/toggle', [TaskController::class, 'toggleStatus'])
            ->middleware('can:update,task')
            ->name('tasks.toggle');
    });
});
