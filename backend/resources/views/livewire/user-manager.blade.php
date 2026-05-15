<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Gestión de Usuarios') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">Administración de Usuarios</h3>
                    <p class="text-gray-500 text-sm">Gestiona quién tiene acceso y qué rol desempeña.</p>
                </div>
                <a href="{{ route('users.create') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg shadow-lg shadow-red-200 transition-all flex items-center">
                    <i class="fa-solid fa-user-plus mr-2"></i> Nuevo Usuario
                </a>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">ID</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Nombre</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Teléfono</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Rol</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Creado</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 font-bold">#{{ $user->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">{{ $user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <span class="text-xs text-gray-400">{{ $user->country_code }}</span> {{ $user->phone_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-bold">
                                        {{ $user->role->name ?? 'Sin Rol' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('users.edit', $user->id) }}" class="text-blue-600 hover:text-blue-900 mr-4 transition-colors">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <button wire:click="confirmDelete({{ $user->id }})" class="text-red-500 hover:text-red-700 transition-colors">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
