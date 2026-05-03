<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <!-- Название текущей доски -->
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $board->name }} 
                <span class="text-sm font-normal text-gray-500 ml-2">/ {{ $space->name }}</span>
            </h2>

            <div class="flex space-x-3">
                <!-- Кнопка редактирования пространства -->
                <a href="{{ route('spaces.edit', $space) }}" class="px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition shadow-sm text-sm font-medium">
                    Настройки пространства
                </a>
                
                <!-- Кнопка создания новой доски -->
                <a href="{{ route('boards.create', ['space' => $space->id]) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition duration-150 ease-in-out shadow-sm text-sm font-medium">
                    + Новая доска
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 h-[calc(100vh-140px)] overflow-hidden">
        <div class="max-w-[98%] mx-auto sm:px-6 lg:px-8 h-full">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex h-full">
                
                <!-- ЛЕВАЯ ЧАСТЬ: Список досок (Sidebar) -->
                <div class="w-64 bg-gray-50 border-r border-gray-200 flex flex-col flex-shrink-0">
                    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Доски
                        </h3>
                    </div>
                    
                    <div class="flex-1 overflow-y-auto p-2 space-y-1">
                        @forelse($space->boards as $navBoard)
                            <!-- Контейнер с относительным позиционированием для меню -->
                            <div class="group relative flex items-center justify-between px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150 
                                {{ $board->id === $navBoard->id ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                                
                                <!-- Ссылка на доску -->
                                <a href="{{ route('boards.show', ['space' => $space->id, 'board' => $navBoard->id]) }}" 
                                class="flex-1 truncate mr-2 z-10">
                                    {{ $navBoard->name }}
                                </a>

                                <!-- Кнопка с тремя точками (меню) -->
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" 
                                            @click.outside="open = false"
                                            class="opacity-0 group-hover:opacity-100 focus:opacity-100 p-1 rounded hover:bg-black/10 transition-opacity z-20">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-500">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM18.75 12a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                                        </svg>
                                    </button>

                                    <!-- Выпадающее меню -->
                                    <div x-show="open" 
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95"
                                        class="absolute right-0 top-full mt-1 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50 origin-top-right">
                                        
                                        <!-- Форма редактирования имени -->
                                        <form action="{{ route('boards.update', ['space' => $space->id, 'board' => $navBoard->id]) }}" method="POST" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="name" value="{{ $navBoard->name }}" 
                                                class="w-full border-gray-300 rounded-md shadow-sm text-xs mb-1 focus:border-indigo-500 focus:ring-indigo-500"
                                                onclick="event.stopPropagation()">
                                            <button type="submit" class="w-full text-left text-xs text-indigo-600 hover:text-indigo-900 font-medium">
                                                Сохранить имя
                                            </button>
                                        </form>

                                        <div class="border-t border-gray-100 my-1"></div>

                                        <!-- Форма удаления -->
                                        <form action="{{ route('boards.destroy', ['space' => $space->id, 'board' => $navBoard->id]) }}" method="POST" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Удалить доску и все задачи в ней?')" class="w-full text-left">
                                                Удалить доску
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-3 py-2 text-sm text-gray-400 italic">
                                Нет других досок
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- ПРАВАЯ ЧАСТЬ: Канбан-доска (Задачи) -->
                <div class="flex-1 overflow-x-auto overflow-y-hidden bg-gray-100 p-6">
                    
                    <!-- Контейнер для колонок -->
                    <div class="flex h-full items-start space-x-4 min-w-max pb-4">
                        
                        {{-- КОЛОНКА 1: TO DO --}}
                        <div class="w-80 flex flex-col bg-gray-200/50 rounded-xl max-h-[calc(100vh-220px)] shadow-sm border border-gray-200/50">
                            <div class="p-3 font-semibold text-gray-700 flex justify-between items-center border-b border-gray-300/50">
                                <span>To Do</span>
                                <span class="bg-gray-300/50 text-gray-600 text-xs px-2 py-0.5 rounded-full">
                                    {{ count($tasks['todo'] ?? []) }}
                                </span>
                            </div>
                            
                            <div class="flex-1 overflow-y-auto px-2 pb-2 space-y-2 custom-scrollbar sortable-list" data-status="todo">
                                @foreach($tasks['todo'] ?? [] as $task)
                                    <!-- Карточка задачи (DIV вместо BUTTON) -->
                                    <div 
                                        data-id="{{ $task->id }}"
                                        onclick="document.getElementById('modal-task-{{ $task->id }}').classList.remove('hidden')"
                                        class="relative w-full bg-white p-3 rounded-lg shadow-sm hover:shadow-md transition group cursor-pointer border-l-4 border-blue-500 mb-2"
                                    >
                                        <h4 class="text-sm font-medium text-gray-900 break-words pr-6">{{ $task->title }}</h4>

                                        
                                            <!-- Вывод описания (если есть) -->
                                        @if($task->description)
                                            <p class="text-xs text-gray-500 mt-1 line-clamp-2 break-words">
                                                {{ $task->description }}
                                            </p>
                                        @endif

                                        <!-- Кнопка удаления -->
                                        <form action="{{ route('tasks.destroy', ['space' => $space, 'board' => $board, 'task' => $task]) }}" method="POST" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity z-10" onclick="event.stopPropagation()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Удалить задачу?')" class="text-red-500 hover:text-red-700 p-1 bg-white rounded shadow-sm hover:bg-red-50">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- МОДАЛЬНОЕ ОКНО -->
                                    <div id="modal-task-{{ $task->id }}" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center backdrop-blur-sm">
                                        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white transform transition-all">
                                            <div class="mt-3">
                                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Редактировать задачу</h3>
                                                
                                                <form action="{{ route('tasks.update', ['space' => $space, 'board' => $board, 'task' => $task]) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    
                                                    <div class="mb-4">
                                                        <label class="block text-gray-700 text-sm font-bold mb-2">Название</label>
                                                        <input type="text" name="title" value="{{ $task->title }}" required
                                                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                                    </div>

                                                    <div class="mb-4">
                                                        <label class="block text-gray-700 text-sm font-bold mb-2">Описание</label>
                                                        <textarea name="description" rows="4" placeholder="Добавьте описание..."
                                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ $task->description }}</textarea>
                                                    </div>

                                                    <div class="mb-4">
                                                        <label class="block text-gray-700 text-sm font-bold mb-2">Статус</label>
                                                        <select name="status" class="shadow border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                                            <option value="todo" {{ $task->status == 'todo' ? 'selected' : '' }}>To Do</option>
                                                            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                            <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>Done</option>
                                                        </select>
                                                    </div>

                                                    <div class="items-center px-4 py-3 flex justify-end space-x-2">
                                                        <button type="button" onclick="document.getElementById('modal-task-{{ $task->id }}').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md hover:bg-gray-200 focus:outline-none">
                                                            Отмена
                                                        </button>
                                                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                            Сохранить
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="p-2">
                                <form action="{{ route('tasks.store', ['space' => $space, 'board' => $board]) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="todo">
                                    <input type="text" name="title" placeholder="+ Добавить задачу" required
                                           class="w-full bg-transparent border-none focus:ring-0 text-sm placeholder-gray-500 text-gray-700 p-2 hover:bg-gray-100 rounded-lg transition">
                                </form>
                            </div>
                        </div>

                        {{-- КОЛОНКА 2: IN PROGRESS --}}
                        <div class="w-80 flex flex-col bg-gray-200/50 rounded-xl max-h-[calc(100vh-220px)] shadow-sm border border-gray-200/50">
                            <div class="p-3 font-semibold text-gray-700 flex justify-between items-center border-b border-gray-300/50">
                                <span>In Progress</span>
                                <span class="bg-gray-300/50 text-gray-600 text-xs px-2 py-0.5 rounded-full">
                                    {{ count($tasks['in_progress'] ?? []) }}
                                </span>
                            </div>
                            
                            <div class="flex-1 overflow-y-auto px-2 pb-2 space-y-2 custom-scrollbar sortable-list" data-status="in_progress">
                                @foreach($tasks['in_progress'] ?? [] as $task)
                                    <!-- Карточка задачи (DIV вместо BUTTON) -->
                                    <div 
                                        data-id="{{ $task->id }}"
                                        onclick="document.getElementById('modal-task-{{ $task->id }}').classList.remove('hidden')"
                                        class="relative w-full bg-white p-3 rounded-lg shadow-sm hover:shadow-md transition group cursor-pointer border-l-4 border-yellow-500 mb-2"
                                    >
                                        <h4 class="text-sm font-medium text-gray-900 break-words pr-6">{{ $task->title }}</h4>
                                        
                                        @if($task->description)
                                            <p class="text-xs text-gray-500 mt-1 line-clamp-2 break-words">
                                                {{ $task->description }}
                                            </p>
                                        @endif

                                        <!-- Кнопка удаления -->
                                        <form action="{{ route('tasks.destroy', ['space' => $space, 'board' => $board, 'task' => $task]) }}" method="POST" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity z-10" onclick="event.stopPropagation()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Удалить задачу?')" class="text-red-500 hover:text-red-700 p-1 bg-white rounded shadow-sm hover:bg-red-50">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- МОДАЛЬНОЕ ОКНО -->
                                    <div id="modal-task-{{ $task->id }}" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center backdrop-blur-sm">
                                        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white transform transition-all">
                                            <div class="mt-3">
                                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Редактировать задачу</h3>
                                                
                                                <form action="{{ route('tasks.update', ['space' => $space, 'board' => $board, 'task' => $task]) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    
                                                    <div class="mb-4">
                                                        <label class="block text-gray-700 text-sm font-bold mb-2">Название</label>
                                                        <input type="text" name="title" value="{{ $task->title }}" required
                                                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                                    </div>

                                                    <div class="mb-4">
                                                        <label class="block text-gray-700 text-sm font-bold mb-2">Описание</label>
                                                        <textarea name="description" rows="4" placeholder="Добавьте описание..."
                                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ $task->description }}</textarea>
                                                    </div>

                                                    <div class="mb-4">
                                                        <label class="block text-gray-700 text-sm font-bold mb-2">Статус</label>
                                                        <select name="status" class="shadow border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                                            <option value="todo" {{ $task->status == 'todo' ? 'selected' : '' }}>To Do</option>
                                                            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                            <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>Done</option>
                                                        </select>
                                                    </div>

                                                    <div class="items-center px-4 py-3 flex justify-end space-x-2">
                                                        <button type="button" onclick="document.getElementById('modal-task-{{ $task->id }}').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md hover:bg-gray-200 focus:outline-none">
                                                            Отмена
                                                        </button>
                                                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                            Сохранить
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="p-2">
                                <form action="{{ route('tasks.store', ['space' => $space, 'board' => $board]) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="in_progress">
                                    <input type="text" name="title" placeholder="+ Добавить задачу" required
                                           class="w-full bg-transparent border-none focus:ring-0 text-sm placeholder-gray-500 text-gray-700 p-2 hover:bg-gray-100 rounded-lg transition">
                                </form>
                            </div>
                        </div>

                        {{-- КОЛОНКА 3: DONE --}}
                        <div class="w-80 flex flex-col bg-gray-200/50 rounded-xl max-h-[calc(100vh-220px)] shadow-sm border border-gray-200/50">
                            <div class="p-3 font-semibold text-gray-700 flex justify-between items-center border-b border-gray-300/50">
                                <span>Done</span>
                                <span class="bg-gray-300/50 text-gray-600 text-xs px-2 py-0.5 rounded-full">
                                    {{ count($tasks['done'] ?? []) }}
                                </span>
                            </div>
                            
                            <div class="flex-1 overflow-y-auto px-2 pb-2 space-y-2 custom-scrollbar sortable-list" data-status="done">
                                @foreach($tasks['done'] ?? [] as $task)
                                    <!-- Карточка задачи (DIV вместо BUTTON) -->
                                    <div 
                                        data-id="{{ $task->id }}"
                                        onclick="document.getElementById('modal-task-{{ $task->id }}').classList.remove('hidden')"
                                        class="relative w-full bg-white p-3 rounded-lg shadow-sm hover:shadow-md transition group cursor-pointer border-l-4 border-green-500 mb-2 opacity-75 hover:opacity-100"
                                    >
                                        <h4 class="text-sm font-medium text-gray-900 break-words pr-6 line-through decoration-gray-400">{{ $task->title }}</h4>
                                        
                                        @if($task->description)
                                            <p class="text-xs text-gray-500 mt-1 line-clamp-2 break-words">
                                                {{ $task->description }}
                                            </p>
                                        @endif

                                        <!-- Кнопка удаления -->
                                        <form action="{{ route('tasks.destroy', ['space' => $space, 'board' => $board, 'task' => $task]) }}" method="POST" class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity z-10" onclick="event.stopPropagation()">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Удалить задачу?')" class="text-red-500 hover:text-red-700 p-1 bg-white rounded shadow-sm hover:bg-red-50">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- МОДАЛЬНОЕ ОКНО -->
                                    <div id="modal-task-{{ $task->id }}" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center backdrop-blur-sm">
                                        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white transform transition-all">
                                            <div class="mt-3">
                                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Редактировать задачу</h3>
                                                
                                                <form action="{{ route('tasks.update', ['space' => $space, 'board' => $board, 'task' => $task]) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    
                                                    <div class="mb-4">
                                                        <label class="block text-gray-700 text-sm font-bold mb-2">Название</label>
                                                        <input type="text" name="title" value="{{ $task->title }}" required
                                                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                                    </div>

                                                    <div class="mb-4">
                                                        <label class="block text-gray-700 text-sm font-bold mb-2">Описание</label>
                                                        <textarea name="description" rows="4" placeholder="Добавьте описание..."
                                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ $task->description }}</textarea>
                                                    </div>

                                                    <div class="mb-4">
                                                        <label class="block text-gray-700 text-sm font-bold mb-2">Статус</label>
                                                        <select name="status" class="shadow border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                                            <option value="todo" {{ $task->status == 'todo' ? 'selected' : '' }}>To Do</option>
                                                            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                            <option value="done" {{ $task->status == 'done' ? 'selected' : '' }}>Done</option>
                                                        </select>
                                                    </div>

                                                    <div class="items-center px-4 py-3 flex justify-end space-x-2">
                                                        <button type="button" onclick="document.getElementById('modal-task-{{ $task->id }}').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md hover:bg-gray-200 focus:outline-none">
                                                            Отмена
                                                        </button>
                                                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                            Сохранить
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="p-2">
                                <form action="{{ route('tasks.store', ['space' => $space, 'board' => $board]) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="done">
                                    <input type="text" name="title" placeholder="+ Добавить задачу" required
                                           class="w-full bg-transparent border-none focus:ring-0 text-sm placeholder-gray-500 text-gray-700 p-2 hover:bg-gray-100 rounded-lg transition">
                                </form>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const lists = document.querySelectorAll('.sortable-list');

        lists.forEach(list => {
            new Sortable(list, {
                group: 'kanban-board',
                animation: 150,
                ghostClass: 'opacity-50',
                
                onEnd: function (evt) {
                    // Если элемент вернулся на то же место (не было реального изменения), ничего не делаем
                    if (evt.oldIndex === evt.newIndex && evt.from === evt.to) {
                        return;
                    }

                    const itemEl = evt.item;
                    const taskId = itemEl.dataset.id;
                    const newStatus = evt.to.dataset.status; 
                    
                    // Собираем порядок ID в новой колонке
                    const newOrderInColumn = Array.from(evt.to.children)
                        .filter(el => el.dataset.id) 
                        .map(el => el.dataset.id);

                    console.log('Отправка данных:', { items: newOrderInColumn, status: newStatus });

                    fetch('{{ route("tasks.sort") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            items: newOrderInColumn,
                            status: newStatus
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            // Если ошибка (4xx или 5xx), читаем текст ошибки
                            return response.text().then(text => { throw new Error(text || response.statusText) });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Успех:', data);
                        // Здесь можно добавить визуальное обновление, если нужно
                    })
                    .catch(error => {
                        console.error('Ошибка сохранения порядка:', error);
                        alert('Не удалось сохранить порядок. Проверьте консоль (F12) для деталей.');
                    });
                }
            });
        });
    });
</script>
</x-app-layout>