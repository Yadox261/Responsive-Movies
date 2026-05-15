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
                        </button>
                    </li>
                    <li class="me-2">
                        <button type="button" wire:click="setTab('multimedia')" class="inline-flex items-center p-4 border-b-2 rounded-t-lg transition-all {{ $activeTab == 'multimedia' ? 'text-red-600 border-red-600 active font-bold' : 'text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                            <i class="fa-solid fa-photo-film mr-2"></i> Multimedia
                        </button>
                    </li>
                    <li class="me-2">
                        <button type="button" wire:click="setTab('reparto')" class="inline-flex items-center p-4 border-b-2 rounded-t-lg transition-all {{ $activeTab == 'reparto' ? 'text-red-600 border-red-600 active font-bold' : 'text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                            <i class="fa-solid fa-user-group mr-2"></i> Reparto y Detalles
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
                </div>
                @endif

                <!-- SECCIÓN MULTIMEDIA -->
                @if($activeTab == 'multimedia')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Póster Vertical</label>
                        <div class="relative group">
                            <div class="w-full h-80 bg-gray-100 rounded-2xl flex items-center justify-center overflow-hidden border-2 border-dashed border-gray-200 group-hover:border-red-300 transition-colors">
                                @if($poster)
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
                                @if($banner)
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
