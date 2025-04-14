<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Мои задачи') }}
            </h2>
            <a href="{{ route('dashboard') }}" class="text-blue-500 hover:text-blue-700">
                Назад в Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <form method="POST" action="{{ route('tasks.store') }}" class="mb-6">
                        @csrf
                        <div class="flex gap-2">
                            <input type="text" name="name" required
                                   class="flex-1 px-4 py-2 border rounded focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Введите название задачи">
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                Добавить
                            </button>
                        </div>
                    </form>

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
                                            <select name="status" onchange="this.form.submit()"
                                                    class="px-2 py-1 border rounded focus:ring-blue-500 focus:border-blue-500">
                                                <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Ожидание</option>
                                                <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>В процессе</option>
                                                <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Выполнено</option>
                                            </select>
                                        </form>

                                        <form method="GET" action="{{ route('tasks.index') }}">
                                            <input type="hidden" name="edit" value="{{ $task->id }}">
                                            <button type="submit"
                                                    class="text-blue-500 hover:text-blue-700">
                                                ✎
                                            </button>
                                        </form>

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
                    @else
                        <p class="text-gray-500 text-center py-4">У вас пока нет задач</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(request()->has('edit'))
        @php $taskToEdit = $tasks->find(request()->edit); @endphp
        @if($taskToEdit)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                    <form method="POST" action="{{ route('tasks.update', $taskToEdit) }}" class="p-6">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="edit-name" class="block text-gray-700 mb-2">Название задачи</label>
                            <input type="text" id="edit-name" name="name" required
                                   value="{{ old('name', $taskToEdit->name) }}"
                                   class="w-full px-4 py-2 border rounded focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        @if($errors->any())
                            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="flex justify-end gap-2">
                            <a href="{{ route('tasks.index') }}"
                               class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">
                                Отмена
                            </a>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                Сохранить
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endif
</x-app-layout>
