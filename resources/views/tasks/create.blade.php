<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Добавление задачи') }}
            </h2>
            <a href="{{ route('tasks.index') }}" class="text-blue-500 hover:text-blue-700">
                Назад к списку
            </a>
        </div>
    </x-slot>
    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('tasks.store') }}">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Название задачи
                            </label>
                            <input type="text" name="name" required
                                   class="w-full px-4 py-2 border rounded focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Введите название задачи">
                        </div>
                        <input type="hidden" name="status" value="pending">
                        <div class="flex items-center gap-2">
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-500 text-black rounded hover:bg-blue-600">
                                Создать
                            </button>
                            <a href="{{ route('tasks.index') }}"
                               class="px-4 py-2 border rounded text-gray-700 hover:bg-gray-100">
                                Отмена
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
