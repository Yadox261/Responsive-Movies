<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
            <form wire:submit.prevent="store">
                <div class="bg-white px-8 pt-8 pb-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-800" id="modal-title">
                            {{ $user_id ? 'Editar Usuario' : 'Nuevo Usuario' }}
                        </h3>
                        <button type="button" wire:click="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fa-solid fa-xmark text-2xl"></i>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <!-- Nombre -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wide">Nombre Completo</label>
                            <input type="text" wire:model="name" class="w-full px-4 py-2.5 rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 transition-all" placeholder="Ej. Juan Pérez">
                            @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wide">Correo Electrónico</label>
                            <input type="email" wire:model="email" class="w-full px-4 py-2.5 rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 transition-all" placeholder="juan@ejemplo.com">
                            @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Teléfono y Código de País -->
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-1">
                                <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wide">País</label>
                                <input type="text" wire:model="country_code" class="w-full px-4 py-2.5 rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 transition-all" placeholder="+52">
                                @error('country_code') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wide">Teléfono</label>
                                <input type="text" wire:model="phone_number" class="w-full px-4 py-2.5 rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 transition-all" placeholder="5512345678">
                                @error('phone_number') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Rol -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wide">Rol Asignado</label>
                            <select wire:model="role_id" class="w-full px-4 py-2.5 rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 transition-all">
                                <option value="">Selecciona un rol</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1 uppercase tracking-wide">
                                Contraseña {{ $user_id ? '(Opcional)' : '' }}
                            </label>
                            <input type="password" wire:model="password" class="w-full px-4 py-2.5 rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 transition-all" placeholder="********">
                            @if($user_id)
                                <p class="text-[10px] text-gray-400 mt-1 italic">Deja en blanco para mantener la actual.</p>
                            @endif
                            @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-6 flex justify-end gap-3">
                    <button type="button" wire:click="closeModal()" class="px-6 py-2.5 rounded-xl text-gray-600 font-bold hover:bg-gray-200 transition-all">
                        Cancelar
                    </button>
                    <button type="submit" class="bg-red-600 px-8 py-2.5 rounded-xl text-white font-bold hover:bg-red-700 shadow-lg shadow-red-200 transition-all">
                        {{ $user_id ? 'Actualizar' : 'Crear Usuario' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
