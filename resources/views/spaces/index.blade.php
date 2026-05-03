<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight tracking-widest uppercase">
                {{ __('Kanban Board') }}
            </h2>
            
            <a href="{{ route('spaces.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition duration-150 ease-in-out shadow-sm text-sm font-medium">
                + Новое пространство
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ваши рабочие пространства:</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                       
                        @forelse($spaces as $space)
                            <a href="{{ route('spaces.show', $space) }}" class="block group">
                                <div class="border border-gray-200 rounded-xl p-6 hover:border-indigo-500 hover:shadow-md transition duration-200 bg-gray-50 group-hover:bg-white h-full flex flex-col justify-between">
                                    <div>
                                        <h4 class="text-xl font-semibold text-gray-800 group-hover:text-indigo-600 transition">
                                            {{ $space->name }}
                                        </h4>
                                        <p class="text-sm text-gray-500 mt-2 line-clamp-2">
                                            {{ $space->description ?? 'Нет описания' }}
                                        </p>
                                    </div>
                                    <div class="mt-4 text-xs text-gray-400">
                                        Обновлено: {{ $space->updated_at->diffForHumans() }}
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="col-span-full text-center py-10 text-gray-500">
                                У вас пока нет пространств. Создайте первое!
                            </div>
                        @endforelse 
                    
                </div>
            </div>

        </div>
    </div>
</x-app-layout>