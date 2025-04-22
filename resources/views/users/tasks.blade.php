<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Задачи пользователя: {{ $user->name }}
            </h2>
            <a href="{{ route('users.index') }}" class="text-blue-500 hover:text-blue-700">
                Назад к списку
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($tasks->count())
                        <div class="space-y-4">
                            @foreach($tasks as $task)
                                <div class="p-4 border rounded flex justify-between items-center">
                                    <span @class(['line-through' => $task->status === \App\Enums\TaskStatus::COMPLETED])>
                                        {{ $task->name }}
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        {{ $task->status->label() }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">У пользователя нет задач</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
