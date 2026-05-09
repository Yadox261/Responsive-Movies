<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h1 class="text-2xl font-bold mb-4">¡Bienvenido de nuevo, {{ Auth::user()->name }}!</h1>
                <p class="text-gray-600 mb-6">Desde aquí puedes gestionar tu catálogo de películas y ver las estadísticas generales.</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Card 1 -->
                    <div class="bg-blue-500 text-white p-6 rounded-lg shadow-lg">
                        <h3 class="text-lg font-semibold">Total de Películas</h3>
                        <p class="text-3xl font-bold">{{ \App\Models\Movie::count() }}</p>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-green-500 text-white p-6 rounded-lg shadow-lg">
                        <h3 class="text-lg font-semibold">Categorías</h3>
                        <p class="text-3xl font-bold">{{ \App\Models\Movie::distinct('genre')->count() }}</p>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-purple-500 text-white p-6 rounded-lg shadow-lg">
                        <h3 class="text-lg font-semibold">Último Estreno</h3>
                        <p class="text-xl font-bold">{{ \App\Models\Movie::latest('release_year')->first()?->title ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="mt-8">
                    <a href="{{ route('movies.index') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
                        Ir a Gestionar Películas
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
