<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Мои задачи') }}
            </h2>
            <div class="space-x-4">
                <a href="{{ route('tasks.create') }}" class="text-blue-500 hover:text-blue-700">
                    Добавить задачу
                </a>
                <a href="{{ route('dashboard') }}" class="text-blue-500 hover:text-blue-700">
                    Назад в Dashboard
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <form method="GET" action="{{ route('tasks.index') }}" class="mb-6">
                <div class="flex gap-4 items-center">
                    <input type="text"
                           name="search"
                           placeholder="Поиск..."
                           value="{{ request('search') }}"
                           class="px-4 py-2 border rounded focus:ring-blue-500 focus:border-blue-500 flex-1">

                    <select name="status" class="px-4 py-2 border rounded focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Все статусы</option>
                        @foreach(App\Enums\TaskStatus::cases() as $statusOption)
                            <option value="{{ $statusOption->value }}"
                                {{ request('status') == $statusOption->value ? 'selected' : '' }}>
                                {{ $statusOption->label() }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">
                        Применить
                    </button>
                    <a href="{{ route('tasks.index') }}"
                       class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">
                        Сбросить
                    </a>
                </div>
            </form>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($errors->any())
                        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($tasks->count())
                        <div class="space-y-3">
                            @foreach($tasks as $task)
                                <div class="flex items-center justify-between p-3 border rounded hover:bg-gray-50">
                                    <div class="flex items-center flex-1">
                                        <span @class([
                                            'flex-1 ml-3',
                                            'line-through text-gray-400' => $task->status === 'completed'
                                        ])>
                                            {{ $task->name }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <form method="POST" action="{{ route('tasks.toggle', $task) }}" class="flex items-center gap-2">
                                            @csrf
                                            <select name="status" class="px-2 py-1 border rounded focus:ring-blue-500 focus:border-blue-500">
                                                @foreach(App\Enums\TaskStatus::cases() as $status)
                                                    <option value="{{ $status->value }}" {{ $task->status->value === $status->value ? 'selected' : '' }}>
                                                        {{ $status->label() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit"
                                                    class="px-3 py-1 bg-blue-500 text-black rounded hover:bg-blue-600 transition-colors">
                                                Сохранить
                                            </button>
                                        </form>

                                        <a href="{{ route('tasks.edit', $task) }}"
                                           class="text-blue-500 hover:text-blue-700">
                                            ✎
                                        </a>

                                        <form method="POST" action="{{ route('tasks.destroy', $task) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-500 hover:text-red-700"
                                                    onclick="return confirm('Вы уверены?')">
                                                ✖
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-6">
                            {{ $tasks->links() }}
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">У вас пока нет задач</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
