<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Создать новое пространство') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Форма создания -->
                    <form method="POST" action="{{ route('spaces.store') }}">
                        @csrf

                        <!-- Название пространства -->
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Название')" />
                            <x-text-input 
                                id="name" 
                                class="block mt-1 w-full" 
                                type="text" 
                                name="name" 
                                :value="old('name')" 
                                required 
                                autofocus 
                                placeholder="Например: Личные задачи, Работа, Учеба"
                            />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Описание -->
                        <div class="mb-6">
                            <x-input-label for="description" :value="__('Описание (необязательно)')" />
                            <textarea 
                                id="description" 
                                name="description" 
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                rows="4"
                                placeholder="Для чего это пространство?..."
                            >{{ old('description') }}</textarea>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('spaces.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline mr-4">
                                Отмена
                            </a>

                            <x-primary-button class="ms-4 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition duration-150 ease-in-out shadow-sm text-sm font-medium">
                                {{ __('Создать пространство') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>