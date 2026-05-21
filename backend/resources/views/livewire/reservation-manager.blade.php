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
                        <button type="button" wire:click="openCreateModal" class="inline-flex items-center px-4 py-2 text-sm font-extrabold rounded-xl text-white bg-purple-600 hover:bg-purple-700 shadow-md shadow-purple-100 hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                            <i class="fa-solid fa-circle-plus mr-2"></i> Crear Reservación
                        </button>
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
                                    <button wire:click="confirmDelete({{ $reservation->id }})" class="text-red-500 hover:text-red-700 transition-colors" title="Cancelar Reservación">
                                        <i class="fa-solid fa-trash-can text-lg"></i>
                                    </button>
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

    <!-- Modal de Creación de Reservación -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm transition-all duration-300 animate-fade-in">
            <div class="bg-white rounded-3xl overflow-hidden shadow-2xl max-w-lg w-full border border-gray-100 transform transition-all duration-300 scale-100 text-left">
                <!-- Header -->
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4 flex justify-between items-center text-white">
                    <div>
                        <h3 class="text-lg font-extrabold flex items-center gap-2">
                            <i class="fa-solid fa-ticket"></i> Nueva Reservación
                        </h3>
                        <p class="text-xs text-purple-100 mt-0.5">Ingresa los datos para registrar la reservación manualmente.</p>
                    </div>
                    <button type="button" wire:click="closeCreateModal" class="text-white/80 hover:text-white hover:bg-white/10 p-1.5 rounded-full transition-all">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Form -->
                <form wire:submit.prevent="saveReservation">
                    <div class="p-6 space-y-4">
                        @if (session()->has('error'))
                            <div class="p-3 bg-red-50 text-red-700 rounded-lg text-xs font-semibold flex items-center gap-2">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                {{ session('error') }}
                            </div>
                        @endif

                        <!-- Selección de Película -->
                        <div>
                            <label for="movie_id" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Película *</label>
                            <select id="movie_id" wire:model.live="movie_id" class="w-full pl-3 pr-10 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('movie_id') border-red-500 @enderror">
                                <option value="">Selecciona una película</option>
                                @foreach($moviesList as $movie)
                                    <option value="{{ $movie->id }}">{{ $movie->title }}</option>
                                @endforeach
                            </select>
                            @error('movie_id') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Selección de Horario/Función -->
                        <div>
                            <label for="schedule_id" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Función / Horario *</label>
                            <select id="schedule_id" wire:model="schedule_id" class="w-full pl-3 pr-10 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('schedule_id') border-red-500 @enderror" @if(empty($movie_id)) disabled @endif>
                                <option value="">
                                    @if(empty($movie_id))
                                        -- Selecciona una película primero --
                                    @else
                                        Selecciona una función
                                    @endif
                                </option>
                                @foreach($schedulesList as $sch)
                                    <option value="{{ $sch->id }}">{{ $sch->day }} - {{ $sch->time }} (Sala: {{ $sch->room }} | {{ $sch->format }})</option>
                                @endforeach
                            </select>
                            @error('schedule_id') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Fila Nombre y Correo -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Nombre Completo *</label>
                                <input type="text" id="name" wire:model="name" placeholder="Ej. Juan Pérez" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('name') border-red-500 @enderror">
                                @error('name') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Correo Electrónico *</label>
                                <input type="email" id="email" wire:model="email" placeholder="cliente@correo.com" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('email') border-red-500 @enderror">
                                @error('email') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Fila Teléfono y Asientos -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="phone" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">WhatsApp / Celular *</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 text-sm font-semibold select-none">+52</span>
                                    <input type="text" id="phone" wire:model="phone" placeholder="10 dígitos" class="w-full pl-12 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('phone') border-red-500 @enderror">
                                </div>
                                <span class="text-[10px] text-gray-400 mt-1 block">Ingresa sin el prefijo +52 (Ej. 3312345678)</span>
                                @error('phone') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="seats" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Cantidad de Asientos *</label>
                                <input type="number" id="seats" wire:model="seats" min="1" max="10" class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all @error('seats') border-red-500 @enderror">
                                @error('seats') <span class="text-xs text-red-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 rounded-b-3xl">
                        <button type="button" wire:click="closeCreateModal" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-700 bg-white hover:bg-gray-100 border border-gray-200 rounded-xl transition-all">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2 text-sm font-extrabold text-white bg-purple-600 hover:bg-purple-700 shadow-md shadow-purple-100 hover:shadow-lg rounded-xl transition-all flex items-center gap-2">
                            <span wire:loading wire:target="saveReservation" class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-1"></span>
                            <span wire:loading.remove wire:target="saveReservation"><i class="fa-solid fa-check"></i></span>
                            Guardar Reservación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
