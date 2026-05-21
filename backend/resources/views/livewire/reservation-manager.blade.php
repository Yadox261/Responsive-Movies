<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Gestión de Reservaciones') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
            
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <h3 class="text-2xl font-extrabold text-gray-800 flex items-center gap-4 flex-wrap">
                        Listado de Reservaciones
                        <a href="{{ route('reservations.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-extrabold rounded-xl text-white bg-purple-600 hover:bg-purple-700 shadow-md shadow-purple-100 hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                            <i class="fa-solid fa-circle-plus mr-2"></i> Crear Reservación
                        </a>
                    </h3>
                    <p class="text-gray-500 text-sm mt-1">Monitorea y gestiona las solicitudes de boletos de cine en tiempo real o regístralas de forma manual.</p>
                </div>
                <!-- Search Bar -->
                <div class="relative w-full md:w-80">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                    </span>
                    <input 
                        wire:model.live="search" 
                        type="text" 
                        placeholder="Buscar por cliente, correo o película..." 
                        class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                    >
                </div>
            </div>

            <!-- Stats/Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="border border-gray-100 bg-neutral-50 rounded-2xl p-6 flex items-center justify-between shadow-sm">
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Reservaciones</span>
                        <h4 class="text-2xl font-extrabold text-gray-800 mt-1">{{ \App\Models\Reservation::count() }}</h4>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600 shadow-inner">
                        <i class="fa-solid fa-ticket text-xl"></i>
                    </div>
                </div>
                <div class="border border-gray-100 bg-neutral-50 rounded-2xl p-6 flex items-center justify-between shadow-sm">
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Boletos Vendidos</span>
                        <h4 class="text-2xl font-extrabold text-gray-800 mt-1">{{ \App\Models\Reservation::sum('seats') }}</h4>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center text-red-600 shadow-inner">
                        <i class="fa-solid fa-chair text-xl"></i>
                    </div>
                </div>
                <div class="border border-gray-100 bg-neutral-50 rounded-2xl p-6 flex items-center justify-between shadow-sm">
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Películas Reservadas</span>
                        <h4 class="text-2xl font-extrabold text-gray-800 mt-1">{{ \App\Models\Reservation::distinct('movie_id')->count() }}</h4>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 shadow-inner">
                        <i class="fa-solid fa-film text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">ID</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Cliente</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Película</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Función</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Boletos</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Fecha Reg.</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($reservations as $reservation)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 font-bold">#{{ $reservation->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-800">{{ $reservation->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $reservation->email }}</div>
                                    <div class="text-xs text-gray-400"><i class="fa-brands fa-whatsapp text-green-500 mr-1"></i>{{ $reservation->phone }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-gray-700">
                                        {{ $reservation->movie->title ?? 'Película Eliminada' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($reservation->schedule)
                                        <div class="text-xs font-semibold text-purple-700 bg-purple-50 px-2.5 py-1 rounded-md inline-block">
                                            {{ $reservation->schedule->day }} - {{ $reservation->schedule->time }}
                                        </div>
                                        <div class="text-[10px] text-gray-500 mt-1">
                                            Sala: {{ $reservation->schedule->room }} | Formato: {{ $reservation->schedule->format }}
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Horario no disponible</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-extrabold flex items-center justify-center w-8 h-8">
                                        {{ $reservation->seats }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                    {{ $reservation->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('reservations.edit', $reservation->id) }}" class="text-blue-500 hover:text-blue-700 transition-colors" title="Editar Reservación">
                                            <i class="fa-solid fa-pen-to-square text-lg"></i>
                                        </a>
                                        <button wire:click="confirmDelete({{ $reservation->id }})" class="text-red-500 hover:text-red-700 transition-colors" title="Cancelar Reservación">
                                            <i class="fa-solid fa-trash-can text-lg"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500 text-sm">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fa-solid fa-ticket-simple text-4xl text-gray-200 mb-3"></i>
                                        <span>No se encontraron reservaciones para mostrar.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $reservations->links() }}
            </div>

        </div>
    </div>
</div>
