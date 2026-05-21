<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100">
            <form wire:submit.prevent="store">
                <div class="bg-white px-8 pt-8 pb-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-800" id="modal-title">
                            {{ $movie_id ? 'Editar Película' : 'Nueva Película' }}
                        </h3>
                        <button type="button" wire:click="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fa-solid fa-xmark text-2xl"></i>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Título -->
                        <div class="col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Título de la Película</label>
                            <input type="text" wire:model="title" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 transition-all" placeholder="Ej. Interstellar">
                            @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Director -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Director</label>
                            <input type="text" wire:model="director" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 transition-all" placeholder="Christopher Nolan">
                            @error('director') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Reparto -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Reparto</label>
                            <input type="text" wire:model="cast" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 transition-all" placeholder="Sam Worthington, Zoe Saldaña...">
                            @error('cast') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Año, Género y Duración -->
                        <div class="col-span-2 grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Año</label>
                                <input type="number" wire:model="release_year" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 transition-all" placeholder="2024">
                                @error('release_year') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Género</label>
                                <input type="text" wire:model="genre" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 transition-all" placeholder="Sci-Fi">
                                @error('genre') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Duración</label>
                                <input type="text" wire:model="duration" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 transition-all" placeholder="2h 15min">
                                @error('duration') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Sinopsis -->
                        <div class="col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Sinopsis</label>
                            <textarea wire:model="synopsis" rows="3" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-purple-500 focus:ring-purple-500 transition-all" placeholder="Escribe un breve resumen..."></textarea>
                            @error('synopsis') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Subida de Póster -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Póster (Vertical)</label>
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all overflow-hidden relative">
                                    @if ($poster && in_array(strtolower($poster->getClientOriginalExtension()), ['png', 'gif', 'bmp', 'svg', 'jpg', 'jpeg', 'webp', 'avif']))
                                        <img src="{{ $poster->temporaryUrl() }}" class="absolute inset-0 w-full h-full object-cover">
                                    @elseif ($poster_url)
                                        <img src="{{ asset('storage/' . $poster_url) }}" class="absolute inset-0 w-full h-full object-cover">
                                    @else
                                        <i class="fa-solid fa-image text-gray-400 text-2xl mb-2"></i>
                                        <p class="text-xs text-gray-500">Subir Póster</p>
                                    @endif
                                    <input type="file" wire:model="poster" class="hidden" />
                                </label>
                            </div>
                            @error('poster') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Subida de Banner (Espectacular) -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Espectacular (Horizontal)</label>
                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all overflow-hidden relative">
                                    @if ($banner && in_array(strtolower($banner->getClientOriginalExtension()), ['png', 'gif', 'bmp', 'svg', 'jpg', 'jpeg', 'webp', 'avif']))
                                        <img src="{{ $banner->temporaryUrl() }}" class="absolute inset-0 w-full h-full object-cover">
                                    @elseif ($banner_url)
                                        <img src="{{ asset('storage/' . $banner_url) }}" class="absolute inset-0 w-full h-full object-cover">
                                    @else
                                        <i class="fa-solid fa-panorama text-gray-400 text-2xl mb-2"></i>
                                        <p class="text-xs text-gray-500">Subir Banner</p>
                                    @endif
                                    <input type="file" wire:model="banner" class="hidden" />
                                </label>
                            </div>
                            @error('banner') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-6 flex justify-end gap-3">
                    <button type="button" wire:click="closeModal()" class="px-6 py-2.5 rounded-xl text-gray-600 font-bold hover:bg-gray-200 transition-all">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-purple-600 px-8 py-2.5 rounded-xl text-white font-bold hover:bg-purple-700 shadow-lg shadow-purple-200 transition-all">
                        {{ $movie_id ? 'Actualizar Película' : 'Guardar Película' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
