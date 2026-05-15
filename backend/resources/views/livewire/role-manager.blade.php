<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Gestión de Roles') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">Definición de Roles</h3>
                    <p class="text-gray-500 text-sm">Controla los niveles de acceso al sistema.</p>
                </div>
                <a href="{{ route('roles.create') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg shadow-lg shadow-red-200 transition-all flex items-center">
                    <i class="fa-solid fa-plus mr-2"></i> Nuevo Rol
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($roles as $role)
                    <div class="border border-gray-100 rounded-2xl p-6 hover:shadow-lg transition-shadow bg-neutral-50">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center text-red-600">
                                <i class="fa-solid fa-user-shield text-xl"></i>
                            </div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">ID: {{ $role->id }}</span>
                        </div>
                        <h4 class="text-lg font-bold text-gray-800 mb-2">{{ $role->name }}</h4>
                        <p class="text-sm text-gray-500 mb-6">{{ $role->description }}</p>
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('roles.edit', $role->id) }}" class="text-gray-400 hover:text-purple-600 transition-colors"><i class="fa-solid fa-pen"></i></a>
                            <button wire:click="confirmDelete({{ $role->id }})" class="text-gray-400 hover:text-red-500 transition-colors"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
