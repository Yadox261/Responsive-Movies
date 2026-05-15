<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl p-8 border border-gray-100">
            <div class="flex justify-between items-center mb-10">
                <div>
                    <h3 class="text-3xl font-extrabold text-gray-800">
                        {{ $isEdit ? 'Editar Rol' : 'Nuevo Rol del Sistema' }}
                    </h3>
                    <p class="text-gray-500 mt-1 italic">Define las capacidades y permisos de este grupo.</p>
                </div>
                <a href="{{ route('roles.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fa-solid fa-circle-xmark text-3xl"></i>
                </a>
            </div>

            <form wire:submit.prevent="save" class="space-y-8">
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nombre del Rol</label>
                    <input type="text" wire:model="name" class="w-full px-5 py-3 rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm transition-all" placeholder="Ej. Administrador de Contenido">
                    @error('name') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Descripción de Funciones</label>
                    <textarea wire:model="description" rows="5" class="w-full px-5 py-3 rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm transition-all" placeholder="Describe qué puede hacer este rol..."></textarea>
                    @error('description') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                </div>

                <div class="pt-8 border-t border-gray-50 flex justify-end gap-4">
                    <a href="{{ route('roles.index') }}" class="px-8 py-3 rounded-xl text-gray-500 font-bold hover:bg-gray-100 transition-all">
                        Regresar
                    </a>
                    <button type="submit" class="bg-red-600 px-12 py-3 rounded-xl text-white font-black uppercase tracking-widest hover:bg-red-700 shadow-xl shadow-red-200 transition-all transform hover:-translate-y-1">
                        {{ $isEdit ? 'Guardar Cambios' : 'Crear Rol' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
