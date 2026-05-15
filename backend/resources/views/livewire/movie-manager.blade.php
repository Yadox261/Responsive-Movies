@section('title', 'Películas')

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Gestión de Películas') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
            
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">Catálogo de Películas</h3>
                    <p class="text-gray-500 text-sm">Gestiona la información, pósters y banners espectaculares.</p>
                </div>
                <a href="{{ route('movies.create') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg shadow-lg shadow-red-200 transition-all flex items-center">
                    <i class="fa-solid fa-plus mr-2"></i> Nueva Película
                </a>
            </div>

            <!-- Tabs Estilo Medical Appointment (Underline) -->
            <div class="mb-8 border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                    <li class="me-2">
                        <button wire:click="setStatus('todos')" 
                            class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group transition-all {{ $status == 'todos' ? 'text-red-600 border-red-600 active font-bold' : 'text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                            <i class="fa-solid fa-layer-group mr-2 {{ $status == 'todos' ? 'text-red-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                            Todos
                        </button>
                    </li>
                    <li class="me-2">
                        <button wire:click="setStatus('cartelera')" 
                            class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group transition-all {{ $status == 'cartelera' ? 'text-red-600 border-red-600 active font-bold' : 'text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                            <i class="fa-solid fa-ticket mr-2 {{ $status == 'cartelera' ? 'text-red-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                            En Cartelera
                        </button>
                    </li>
                    <li class="me-2">
                        <button wire:click="setStatus('proximamente')" 
                            class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group transition-all {{ $status == 'proximamente' ? 'text-red-600 border-red-600 active font-bold' : 'text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                            <i class="fa-solid fa-clock mr-2 {{ $status == 'proximamente' ? 'text-red-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                            Próximamente
                        </button>
                    </li>
                    <li class="me-2">
                        <button wire:click="setStatus('archivadas')" 
                            class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group transition-all {{ $status == 'archivadas' ? 'text-red-600 border-red-600 active font-bold' : 'text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                            <i class="fa-solid fa-box-archive mr-2 {{ $status == 'archivadas' ? 'text-red-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                            Archivadas
                        </button>
                    </li>
                </ul>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Póster</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Título</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Director</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Año</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Duración</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($movies as $movie)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <img src="{{ Str::startsWith($movie->poster_url, 'http') ? $movie->poster_url : asset('storage/' . $movie->poster_url) }}" class="w-12 h-16 object-cover rounded shadow-sm border border-gray-100" alt="poster">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">{{ $movie->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $movie->director }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-bold">{{ $movie->release_year }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $movie->duration }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button wire:click="toggleArchive({{ $movie->id }})" class="{{ $status == 'archivadas' ? 'text-green-600 hover:text-green-900' : 'text-amber-500 hover:text-amber-700' }} mr-4 transition-colors" title="{{ $status == 'archivadas' ? 'Restaurar' : 'Archivar' }}">
                                        <i class="fa-solid {{ $status == 'archivadas' ? 'fa-rotate-left' : 'fa-box-archive' }} text-lg"></i>
                                    </button>
                                    <a href="{{ route('movies.edit', $movie->id) }}" class="text-blue-600 hover:text-blue-900 mr-4 transition-colors">
                                        <i class="fa-solid fa-pen-to-square text-lg"></i>
                                    </a>
                                    <button wire:click="confirmDelete({{ $movie->id }})" class="text-red-500 hover:text-red-700 transition-colors">
                                        <i class="fa-solid fa-trash text-lg"></i>
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
