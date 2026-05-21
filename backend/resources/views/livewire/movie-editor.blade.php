<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl p-8 border border-gray-100">
            <div class="flex justify-between items-center mb-10">
                <div>
                    <h3 class="text-3xl font-extrabold text-gray-800">
                        {{ $isEdit ? 'Editar Película' : 'Añadir Nueva Película' }}
                    </h3>
                    <p class="text-gray-500 mt-1 italic">Completa todos los campos para actualizar la cartelera.</p>
                </div>
                <a href="{{ route('movies.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fa-solid fa-circle-xmark text-3xl"></i>
                </a>
            </div>

            <div class="mb-6 border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                    <li class="me-2">
                        <button type="button" wire:click="setTab('general')" class="inline-flex items-center p-4 border-b-2 rounded-t-lg transition-all {{ $activeTab == 'general' ? 'text-red-600 border-red-600 active font-bold' : 'text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                            <i class="fa-solid fa-circle-info mr-2"></i> General
                            @if ($errors->hasAny(['title', 'genre', 'release_year', 'synopsis']))
                                <i class="fa-solid fa-circle-exclamation text-red-600 animate-bounce ml-2 text-base shadow-sm" title="Esta sección contiene errores"></i>
                            @endif
                        </button>
                    </li>
                    <li class="me-2">
                        <button type="button" wire:click="setTab('multimedia')" class="inline-flex items-center p-4 border-b-2 rounded-t-lg transition-all {{ $activeTab == 'multimedia' ? 'text-red-600 border-red-600 active font-bold' : 'text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                            <i class="fa-solid fa-photo-film mr-2"></i> Multimedia
                            @if ($errors->hasAny(['poster', 'banner']))
                                <i class="fa-solid fa-circle-exclamation text-red-600 animate-bounce ml-2 text-base shadow-sm" title="Esta sección contiene errores"></i>
                            @endif
                        </button>
                    </li>
                    <li class="me-2">
                        <button type="button" wire:click="setTab('reparto')" class="inline-flex items-center p-4 border-b-2 rounded-t-lg transition-all {{ $activeTab == 'reparto' ? 'text-red-600 border-red-600 active font-bold' : 'text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                            <i class="fa-solid fa-user-group mr-2"></i> Reparto y Detalles
                            @if ($errors->hasAny(['director', 'cast', 'duration']))
                                <i class="fa-solid fa-circle-exclamation text-red-600 animate-bounce ml-2 text-base shadow-sm" title="Esta sección contiene errores"></i>
                            @endif
                        </button>
                    </li>
                    <li class="me-2">
                        <button type="button" @if($movie_id) wire:click="setTab('horarios')" @else disabled @endif class="inline-flex items-center p-4 border-b-2 rounded-t-lg transition-all {{ !$movie_id ? 'opacity-50 cursor-not-allowed text-gray-400 border-transparent' : ($activeTab == 'horarios' ? 'text-red-600 border-red-600 active font-bold' : 'text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300') }}" title="{{ !$movie_id ? 'Guarda la película primero para gestionar horarios' : '' }}">
                            <i class="fa-solid fa-clock mr-2"></i> Horarios y Salas
                        </button>
                    </li>
                </ul>
            </div>

            <form wire:submit.prevent="save">
                <!-- SECCIÓN GENERAL -->
                @if($activeTab == 'general')
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Título de la Obra</label>
                        <input type="text" wire:model="title" class="w-full px-5 py-3 rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm transition-all" placeholder="Ej. El Caballero de la Noche">
                        @error('title') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Género</label>
                            <input type="text" wire:model="genre" class="w-full px-5 py-3 rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm transition-all">
                            @error('genre') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Año</label>
                            <input type="number" wire:model="release_year" class="w-full px-5 py-3 rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm transition-all">
                            @error('release_year') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Sinopsis / Resumen</label>
                        <textarea wire:model="synopsis" rows="5" class="w-full px-5 py-3 rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm transition-all"></textarea>
                        @error('synopsis') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Toggle Estreno -->
                    <div class="bg-red-50/30 p-5 rounded-2xl border border-red-100 flex items-center justify-between shadow-sm">
                        <div>
                            <h4 class="text-sm font-extrabold text-red-800 flex items-center">
                                <i class="fa-solid fa-star mr-2 animate-pulse text-red-600"></i> ¿Es Película de Estreno?
                            </h4>
                            <p class="text-xs text-gray-500 mt-1">
                                Las películas de estreno permiten programar funciones especiales nocturnas hasta las 11:00 PM.
                            </p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" wire:model="is_premiere" class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-red-600"></div>
                        </label>
                    </div>
                </div>
                @endif

                <!-- SECCIÓN MULTIMEDIA -->
                @if($activeTab == 'multimedia')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Póster Vertical</label>
                        <div class="relative group">
                            <div class="w-full h-80 bg-gray-100 rounded-2xl flex items-center justify-center overflow-hidden border-2 border-dashed border-gray-200 group-hover:border-red-300 transition-colors">
                                @if($poster && in_array(strtolower($poster->getClientOriginalExtension()), ['png', 'gif', 'bmp', 'svg', 'jpg', 'jpeg', 'webp', 'avif']))
                                    <img src="{{ $poster->temporaryUrl() }}" class="w-full h-full object-cover">
                                @elseif($poster_url)
                                    <img src="{{ Str::startsWith($poster_url, 'http') ? $poster_url : asset('storage/'.$poster_url) }}" class="w-full h-full object-cover">
                                @else
                                    <i class="fa-solid fa-image text-4xl text-gray-300"></i>
                                @endif
                                <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity cursor-pointer">
                                    <input type="file" wire:model="poster" class="absolute inset-0 opacity-0 cursor-pointer">
                                    <span class="text-white font-bold"><i class="fa-solid fa-upload mr-2"></i> Cambiar</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Banner Horizontal</label>
                        <div class="relative group">
                            <div class="w-full h-40 bg-gray-100 rounded-2xl flex items-center justify-center overflow-hidden border-2 border-dashed border-gray-200 group-hover:border-red-300 transition-colors">
                                @if($banner && in_array(strtolower($banner->getClientOriginalExtension()), ['png', 'gif', 'bmp', 'svg', 'jpg', 'jpeg', 'webp', 'avif']))
                                    <img src="{{ $banner->temporaryUrl() }}" class="w-full h-full object-cover">
                                @elseif($banner_url)
                                    <img src="{{ Str::startsWith($banner_url, 'http') ? $banner_url : asset('storage/'.$banner_url) }}" class="w-full h-full object-cover">
                                @else
                                    <i class="fa-solid fa-panorama text-4xl text-gray-300"></i>
                                @endif
                                <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity cursor-pointer">
                                    <input type="file" wire:model="banner" class="absolute inset-0 opacity-0 cursor-pointer">
                                    <span class="text-white font-bold"><i class="fa-solid fa-upload mr-2"></i> Cambiar</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 italic">Recomendado: 1920x1080px para mejor visualización.</p>
                    </div>
                </div>
                @endif

                <!-- SECCIÓN REPARTO -->
                @if($activeTab == 'reparto')
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Director</label>
                        <input type="text" wire:model="director" class="w-full px-5 py-3 rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm transition-all">
                        @error('director') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Elenco (Reparto)</label>
                        <textarea wire:model="cast" rows="3" class="w-full px-5 py-3 rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm transition-all" placeholder="Actores principales..."></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Duración</label>
                        <input type="text" wire:model="duration" class="w-full px-5 py-3 rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm transition-all" placeholder="Ej. 2h 30min">
                    </div>
                </div>
                @endif

                <!-- SECCIÓN HORARIOS Y SALAS -->
                @if($activeTab == 'horarios' && $movie_id)
                
                <style>
                    .grid-cols-13 {
                        display: grid;
                        grid-template-columns: repeat(13, minmax(0, 1fr));
                    }
                    .timeline-grid-bg {
                        background-image: repeating-linear-gradient(90deg, rgba(255, 255, 255, 0.03) 0px, rgba(255, 255, 255, 0.03) 1px, transparent 1px, transparent calc(100% / 13));
                    }
                </style>

                <div class="space-y-8 animate-fade-in">
                    <!-- Formulario de Nuevo Horario -->
                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 shadow-sm">
                        <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fa-solid fa-plus-circle text-red-600 mr-2 text-xl"></i> Agregar Nuevo Horario
                        </h4>
                        
                        @if (session()->has('schedule_success'))
                            <div class="mb-4 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-xl shadow-sm text-sm font-bold flex items-center">
                                <i class="fa-solid fa-circle-check mr-2 text-lg text-green-600"></i> {{ session('schedule_success') }}
                            </div>
                        @endif

                        @error('new_time')
                            <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl shadow-sm text-sm font-semibold flex items-start">
                                <i class="fa-solid fa-circle-exclamation mr-3 text-lg text-red-500 mt-0.5 animate-bounce"></i>
                                <div>
                                    <span class="font-extrabold block">¡Límite o Conflicto de Horario!</span>
                                    <span>{{ $message }}</span>
                                </div>
                            </div>
                        @enderror

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Día / Rango</label>
                                <select wire:model="new_day" class="w-full px-4 py-2.5 rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm text-sm bg-white">
                                    <option value="Todos los días">Todos los días</option>
                                    <option value="Lunes a Viernes">Lunes a Viernes</option>
                                    <option value="Fin de Semana">Fin de Semana</option>
                                    <option value="Lunes">Lunes</option>
                                    <option value="Martes">Martes</option>
                                    <option value="Miércoles">Miércoles</option>
                                    <option value="Jueves">Jueves</option>
                                    <option value="Viernes">Viernes</option>
                                    <option value="Sábado">Sábado</option>
                                    <option value="Domingo">Domingo</option>
                                </select>
                                @error('new_day') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Hora de Inicio</label>
                                <input type="time" wire:model="new_time" class="w-full px-4 py-2.5 rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm text-sm bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Sala</label>
                                <select wire:model="new_room" class="w-full px-4 py-2.5 rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm text-sm bg-white">
                                    <option value="Sala 1">Sala 1</option>
                                    <option value="Sala 2">Sala 2</option>
                                    <option value="Sala 3">Sala 3</option>
                                    <option value="Sala 4">Sala 4</option>
                                    <option value="Sala 5">Sala 5</option>
                                    <option value="Sala VIP">Sala VIP</option>
                                    <option value="Sala 3D">Sala 3D</option>
                                    <option value="Sala IMAX">Sala IMAX</option>
                                </select>
                                @error('new_room') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Formato</label>
                                <select wire:model="new_format" class="w-full px-4 py-2.5 rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm text-sm bg-white">
                                    <option value="2D Español">2D Español</option>
                                    <option value="2D Subtitulada">2D Subtitulada</option>
                                    <option value="3D Español">3D Español</option>
                                    <option value="3D Subtitulada">3D Subtitulada</option>
                                    <option value="IMAX Español">IMAX Español</option>
                                    <option value="IMAX Subtitulada">IMAX Subtitulada</option>
                                    <option value="4DX">4DX</option>
                                </select>
                                @error('new_format') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        
                        <div class="mt-4 flex justify-end">
                            <button type="button" wire:click="addSchedule()" class="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-md shadow-red-100 transition-all text-sm flex items-center">
                                <i class="fa-solid fa-circle-plus mr-2"></i> Agregar Horario
                            </button>
                        </div>
                    </div>

                    <!-- LÍNEA DE TIEMPO VISUAL DE SALAS -->
                    <div class="bg-slate-900 text-gray-100 p-6 rounded-2xl shadow-xl border border-slate-800">
                        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
                            <div>
                                <h4 class="text-lg font-bold text-white flex items-center">
                                    <i class="fa-solid fa-chart-gantt text-red-500 mr-2 text-xl animate-pulse"></i> Visualizador Diario de Ocupación por Sala
                                </h4>
                                <p class="text-xs text-gray-400 mt-1">Monitorea el uso de cada sala. Los bloques en color rojo representan la película actual.</p>
                            </div>
                            
                            <!-- Selector de día -->
                            <div class="flex flex-wrap gap-1 bg-slate-800 p-1 rounded-xl border border-slate-700 max-w-full overflow-x-auto">
                                @foreach(['Todos los días', 'Lunes a Viernes', 'Fin de Semana', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $dayOpt)
                                    <button type="button" wire:click="changeViewDay('{{ $dayOpt }}')" 
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all whitespace-nowrap {{ $viewDay === $dayOpt ? 'bg-red-600 text-white shadow-md' : 'text-slate-400 hover:text-slate-200' }}">
                                        {{ $dayOpt }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Eje de Horas (11:00 AM - 12:00 AM) -->
                        <div class="space-y-3">
                            <div class="relative flex items-center text-[10px] text-slate-400 font-mono border-b border-slate-800 pb-2">
                                <span class="w-20 font-bold text-slate-500 uppercase tracking-widest">Sala</span>
                                <div class="flex-1 grid grid-cols-13 text-center pl-4 pr-1">
                                    <span>11:00</span>
                                    <span>12:00</span>
                                    <span>13:00</span>
                                    <span>14:00</span>
                                    <span>15:00</span>
                                    <span>16:00</span>
                                    <span>17:00</span>
                                    <span>18:00</span>
                                    <span>19:00</span>
                                    <span>20:00</span>
                                    <span>21:00</span>
                                    <span>22:00</span>
                                    <span>23:00</span>
                                </div>
                                <span class="w-8 text-right">00:00</span>
                            </div>

                            <!-- Listado de Salas con sus Timelines -->
                            <div class="space-y-4">
                                @foreach($rooms as $room)
                                    <div class="flex items-center hover:bg-slate-800/20 py-1.5 rounded-xl transition-all">
                                        <!-- Nombre de la Sala -->
                                        <div class="w-20 pr-2">
                                            <span class="text-xs font-black text-slate-300 tracking-wider truncate block" title="{{ $room }}">{{ $room }}</span>
                                        </div>
                                        
                                        <!-- Contenedor del eje temporal de la sala -->
                                        <div class="flex-1 h-10 bg-slate-950 rounded-xl relative border border-slate-800/80 shadow-inner ml-4 pr-1 timeline-grid-bg">
                                            @if(isset($timelineData[$room]) && count($timelineData[$room]) > 0)
                                                @foreach($timelineData[$room] as $schedData)
                                                    <!-- Bloque de Película -->
                                                    <div class="absolute top-1 bottom-1 flex flex-col justify-center rounded-lg px-2 text-[9px] truncate transition-all hover:scale-[1.02] hover:z-10 group cursor-pointer {{ $schedData['is_current_movie'] ? 'bg-gradient-to-r from-red-600 to-red-500 text-white font-extrabold shadow-md shadow-red-900/30 border border-red-400' : 'bg-slate-800/90 text-slate-300 border border-slate-700' }}"
                                                        style="left: {{ $schedData['left'] }}%; width: {{ $schedData['width'] }}%;"
                                                        title="{{ $schedData['movie_title'] }} ({{ $schedData['format'] }}): {{ $schedData['time_start'] }} - {{ $schedData['time_end'] }} + 15m limpieza">
                                                        <span class="font-bold truncate">{{ $schedData['movie_title'] }}</span>
                                                        <span class="opacity-95 font-mono text-[8px] mt-0.5">{{ $schedData['time_start'] }} - {{ $schedData['time_end'] }}</span>
                                                    </div>

                                                    <!-- Bloque de Buffer (15 min de limpieza) -->
                                                    <div class="absolute top-1 bottom-1 bg-yellow-500/20 border-l border-dashed border-yellow-500/40 rounded-r-lg"
                                                        style="left: {{ $schedData['buffer_left'] }}%; width: {{ $schedData['buffer_width'] }}%;"
                                                        title="15 min de limpieza">
                                                    </div>
                                                @endforeach
                                            @else
                                                <!-- Indicador de Sala Disponible -->
                                                <div class="absolute inset-0 flex items-center justify-center text-[10px] text-slate-700 italic tracking-wider select-none">
                                                    <i class="fa-regular fa-calendar-check mr-1.5 text-xs text-slate-800"></i> Sala Disponible
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Listado en Tabla Clásica -->
                    <div>
                        <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fa-solid fa-list-check text-gray-600 mr-2 text-xl"></i> Horarios Configurados para esta Película
                        </h4>
                        
                        @if($schedules && count($schedules) > 0)
                            <div class="overflow-hidden rounded-2xl border border-gray-100 shadow-sm">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Días</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Hora</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Sala</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-400 uppercase tracking-widest">Formato</th>
                                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        @foreach($schedules as $sched)
                                            <tr class="hover:bg-gray-50/50 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">{{ $sched->day }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                    <span class="bg-red-50 text-red-600 px-2.5 py-1 rounded-lg text-xs font-bold border border-red-100"><i class="fa-regular fa-clock mr-1"></i>{{ $sched->time }}</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $sched->room }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-semibold">{{ $sched->format }}</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <button type="button" wire:click="deleteSchedule({{ $sched->id }})" class="text-red-500 hover:text-red-700 transition-colors bg-red-50 hover:bg-red-100 p-2 rounded-lg" title="Eliminar Horario">
                                                        <i class="fa-solid fa-trash-can text-base"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-10 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                                <i class="fa-solid fa-calendar-xmark text-4xl text-gray-300 mb-3 block"></i>
                                <p class="text-gray-500 font-medium">No se han configurado horarios para esta película todavía.</p>
                                <p class="text-xs text-gray-400 mt-1">Usa el formulario de arriba para añadir el primero.</p>
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                <div class="pt-8 border-t border-gray-100 flex justify-end gap-4">
                    <a href="{{ route('movies.index') }}" class="px-8 py-3 rounded-xl text-gray-500 font-bold hover:bg-gray-100 transition-all">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-red-600 px-12 py-3 rounded-xl text-white font-black uppercase tracking-widest hover:bg-red-700 shadow-xl shadow-red-200 transition-all transform hover:-translate-y-1">
                        {{ $isEdit ? 'Guardar Cambios' : 'Publicar Película' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
