<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Редактирование пространства') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form method="POST" action="{{ route('spaces.update', $space) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Название')" />
                            <x-text-input 
                                id="name" 
                                class="block mt-1 w-full" 
                                type="text" 
                                name="name" 
                                :value="old('name', $space->name)" 
                                required 
                                autofocus 
                            />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mb-6">
                            <x-input-label for="description" :value="__('Описание')" />
                            <textarea 
                                id="description" 
                                name="description" 
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                rows="4"
                            >{{ old('description', $space->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('spaces.show', $space) }}" class="text-sm text-gray-600 hover:text-gray-900 underline mr-4">
                                Отмена
                            </a>
                            <x-primary-button class="ms-4">
                                {{ __('Сохранить изменения') }}
                            </x-primary-button>
                        </div>
                    </form>
                        
                        <form method="POST" action="{{ route('spaces.destroy', $space) }}" onsubmit="return confirm('Вы уверены? Это действие нельзя отменить.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition duration-150 ease-in-out text-sm font-medium">
                                Удалить пространство
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>