<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl p-8 border border-gray-100">
            <div class="flex justify-between items-center mb-10 text-center md:text-left">
                <div>
                    <h3 class="text-3xl font-extrabold text-gray-800">
                        {{ $isEdit ? 'Modificar Usuario' : 'Registrar Nuevo Miembro' }}
                    </h3>
                    <p class="text-gray-500 mt-1 italic">Asigna roles y credenciales de acceso de forma segura.</p>
                </div>
                <a href="{{ route('users.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fa-solid fa-circle-xmark text-3xl"></i>
                </a>
            </div>

            <form wire:submit.prevent="save" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nombre Completo</label>
                        <input type="text" wire:model="name" class="w-full px-5 py-3 rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm transition-all" placeholder="Ej. Ricardo Milos">
                        @error('name') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Dirección de Email</label>
                        <input type="email" wire:model="email" class="w-full px-5 py-3 rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm transition-all" placeholder="usuario@pelisapp.com">
                        @error('email') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Código de País -->
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Código País</label>
                        <input type="text" wire:model="country_code" class="w-full px-5 py-3 rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm transition-all" placeholder="+52">
                        @error('country_code') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Número Telefónico</label>
                        <input type="text" wire:model="phone_number" class="w-full px-5 py-3 rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm transition-all" placeholder="5512345678">
                        @error('phone_number') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Rol -->
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nivel de Acceso (Rol)</label>
                        <select wire:model="role_id" class="w-full px-5 py-3 rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm transition-all">
                            <option value="">Selecciona un rol</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role_id') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Contraseña -->
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
                            Contraseña {{ $isEdit ? '(Opcional)' : '' }}
                        </label>
                        <input type="password" wire:model="password" class="w-full px-5 py-3 rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm transition-all" placeholder="********">
                        @error('password') <span class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="pt-10 flex justify-end gap-4 border-t border-gray-50 mt-8">
                    <a href="{{ route('users.index') }}" class="px-8 py-3 rounded-xl text-gray-500 font-bold hover:bg-gray-100 transition-all">
                        Descartar
                    </a>
                    <button type="submit" class="bg-red-600 px-12 py-3 rounded-xl text-white font-black uppercase tracking-widest hover:bg-red-700 shadow-xl shadow-red-200 transition-all transform hover:-translate-y-1">
                        {{ $isEdit ? 'Actualizar Usuario' : 'Confirmar Registro' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
