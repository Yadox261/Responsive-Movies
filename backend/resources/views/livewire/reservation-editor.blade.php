<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl p-8 border border-gray-100">
            <!-- Cabecera -->
            <div class="flex justify-between items-center mb-10 text-center md:text-left">
                <div>
                    <h3 class="text-3xl font-extrabold text-gray-800">
                        {{ $isEdit ? 'Modificar Reservación' : 'Registrar Nueva Reservación' }}
                    </h3>
                    <p class="text-gray-500 mt-1 italic">Ingresa los datos para registrar la reservación manualmente en el sistema.</p>
                </div>
                <a href="{{ route('reservations.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors" title="Volver al Listado">
                    <i class="fa-solid fa-circle-xmark text-3xl"></i>
                </a>
            </div>

            <!-- Formulario principal -->
            <form wire:submit.prevent="save" class="space-y-6">
                @if (session()->has('error'))
                    <div class="p-4 bg-red-50 text-red-700 rounded-xl text-sm font-semibold flex items-center gap-3 border border-red-100">
                        <i class="fa-solid fa-circle-exclamation text-lg"></i>
                        {{ session('error') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Película -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Película *</label>
                        <select wire:model.live="movie_id" class="w-full px-5 py-3 rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 shadow-sm transition-all @error('movie_id') border-red-500 @enderror">
                            <option value="">Selecciona una película</option>
                            @foreach($moviesList as $movie)
                                <option value="{{ $movie->id }}">{{ $movie->title }}</option>
                            @endforeach
                        </select>
                        @error('movie_id') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Función / Horario -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Función / Horario *</label>
                        <select wire:model="schedule_id" class="w-full px-5 py-3 rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 shadow-sm transition-all @error('schedule_id') border-red-500 @enderror" @if(empty($movie_id)) disabled @endif>
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
                        @error('schedule_id') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Nombre del Cliente -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nombre Completo del Cliente *</label>
                        <input type="text" wire:model="name" class="w-full px-5 py-3 rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 shadow-sm transition-all @error('name') border-red-500 @enderror" placeholder="Ej. Juan Pérez">
                        @error('name') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Correo Electrónico -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Dirección de Email *</label>
                        <input type="email" wire:model="email" class="w-full px-5 py-3 rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 shadow-sm transition-all @error('email') border-red-500 @enderror" placeholder="cliente@correo.com">
                        @error('email') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <!-- WhatsApp / Teléfono Celular -->
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">WhatsApp / Celular (10 dígitos) *</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-5 text-gray-400 text-sm font-extrabold select-none">+52</span>
                            <input type="text" wire:model="phone" class="w-full pl-14 pr-5 py-3 rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 shadow-sm transition-all @error('phone') border-red-500 @enderror" placeholder="3312345678">
                        </div>
                        <span class="text-[10px] text-gray-400 mt-1.5 block">Ingresa sin la clave +52. Se autocompletará al guardar.</span>
                        @error('phone') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Cantidad de Asientos -->
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Cantidad de Asientos *</label>
                        <input type="number" wire:model="seats" min="1" max="10" class="w-full px-5 py-3 rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 shadow-sm transition-all @error('seats') border-red-500 @enderror">
                        @error('seats') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="pt-10 flex justify-end gap-4 border-t border-gray-50 mt-8">
                    <a href="{{ route('reservations.index') }}" class="px-8 py-3 rounded-xl text-gray-500 font-bold hover:bg-gray-100 transition-all">
                        Descartar
                    </a>
                    <button type="submit" class="bg-purple-600 px-12 py-3 rounded-xl text-white font-black uppercase tracking-widest hover:bg-purple-700 shadow-xl shadow-purple-200 transition-all transform hover:-translate-y-1 flex items-center gap-2">
                        <span wire:loading wire:target="save" class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full"></span>
                        {{ $isEdit ? 'Actualizar Reservación' : 'Registrar Reservación' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
