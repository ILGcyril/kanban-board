<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $space->name }} 
                <span class="text-sm font-normal text-gray-500 ml-2">/ {{ $space->name }}</span>
            </h2>

            <div class="flex space-x-3">
                <!-- Кнопка редактирования пространства (опционально) -->
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

    <div class="py-6" x-data="{ activeBoardId: 1 }"> <!-- Alpine.js для переключения вкладок -->
        <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8 h-[calc(100vh-140px)]">
            
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
                            <div class="group relative flex items-center justify-between px-3 py-2 rounded-md text-sm font-medium transition-colors duration-150 text-gray-700 hover:bg-gray-100">
                                
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

                <!-- ПРАВАЯ ЧАСТЬ: Контент -->
                <div class="flex-1 bg-gray-100 p-6 flex items-center justify-center">
                    
                    <!-- Состояние: Доска не выбрана -->
                    <div class="text-center max-w-md">
                        <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
                            <!-- Иконка доски (SVG) -->
                            <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            Выберите доску или создайте новую
                        </h3>
                        <p class="text-gray-500 mb-6">
                            В этом пространстве пока нет активной доски. Выберите одну из списка слева или создайте новую, чтобы начать работу.
                        </p>
                        
                        @if($boards->isEmpty())
                            <a href="{{ route('boards.create', ['space' => $space->id]) }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Создать первую доску
                            </a>
                        @else
                            <a href="{{ route('boards.create', ['space' => $space->id]) }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Создать новую доску
                            </a>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>