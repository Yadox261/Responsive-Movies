<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PeliculasApp') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>

<body class="font-sans antialiased bg-neutral-primary-soft text-gray-800">
    <x-banner />

    @php
        $Links = [
            [
                'icon' => 'fa-solid fa-gauge',
                'name' => 'Panel Admin',
                'href' => route('dashboard'),
                'active' => request()->routeIs('dashboard'),
            ],
            [
                'header' => 'GESTIÓN',
            ],
            [
                'name' => 'Películas',
                'icon' => 'fa-solid fa-film',
                'href' => route('movies.index'),
                'active' => request()->routeIs('movies.index'),
            ],
            [
                'header' => 'ADMINISTRACIÓN',
            ],
            [
                'name' => 'Usuarios',
                'icon' => 'fa-solid fa-users',
                'href' => route('users.index'),
                'active' => request()->routeIs('users.index'),
            ],
            [
                'name' => 'Roles',
                'icon' => 'fa-solid fa-user-shield',
                'href' => route('roles.index'),
                'active' => request()->routeIs('roles.index'),
            ],
            [
                'name' => 'Reservaciones',
                'icon' => 'fa-solid fa-ticket',
                'href' => route('reservations.index'),
                'active' => request()->routeIs('reservations.index'),
            ],
        ];
    @endphp

    <!-- Navbar -->
    <nav class="fixed top-0 z-50 w-full bg-white border-b border-gray-200">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start">
                    <button data-drawer-target="top-bar-sidebar" data-drawer-toggle="top-bar-sidebar"
                        aria-controls="top-bar-sidebar" type="button"
                        class="sm:hidden text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 rounded-lg text-sm p-2">
                        <span class="sr-only">Abrir sidebar</span>
                        <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                            </path>
                        </svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="flex ms-2 md:me-24">
                        <img src="{{ asset('images/rollo.jpg') }}" class="h-8 me-3" alt="Logo">
                        <span
                            class="self-center text-lg font-semibold whitespace-nowrap text-heading">PeliculasApp</span>
                    </a>
                </div>
                <div class="flex items-center">
                    <div class="flex items-center ms-3">
                        <button type="button"
                            class="flex text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300"
                            aria-expanded="false" data-dropdown-toggle="dropdown-user">
                            <span class="sr-only">Abrir menú de usuario</span>
                            <img class="w-8 h-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                                alt="user photo">
                        </button>
                        <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow"
                            id="dropdown-user">
                            <div class="px-4 py-3" role="none">
                                <p class="text-sm text-gray-900 font-medium" role="none">{{ Auth::user()->name }}</p>
                                <p class="text-sm text-gray-500 truncate" role="none">{{ Auth::user()->email }}</p>
                            </div>
                            <ul class="py-1" role="none">
                                <li><a href="{{ route('profile.show') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mi Perfil</a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" x-data>
                                        @csrf
                                        <button type="submit" @click.prevent="$root.submit();"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Cerrar
                                            Sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- FIX 1: top-16 (64px) en lugar de top-32 (128px), y h-[calc(100vh-64px)] en lugar de h-[calc(100vh-128px)] -->
    <aside id="top-bar-sidebar"
        class="fixed top-16 left-0 z-30 w-64 h-[calc(100vh-64px)] transition-transform -translate-x-full sm:translate-x-0 bg-neutral-primary-soft border-r-4 border-red-500"
        aria-label="Sidebar">
        <div class="h-full px-5 py-4 overflow-y-auto">
            <ul class="space-y-3 font-medium">
                @foreach ($Links as $link)
                    <li>
                        @isset($link['header'])
                            <div class="px-2 py-2 text-xs text-gray-400 uppercase font-bold tracking-wider">
                                {{ $link['header'] }}
                            </div>
                        @else
                            <a href="{{ $link['href'] }}"
                                class="flex items-center px-3 py-2 text-gray-700 rounded-lg hover:bg-purple-100 hover:text-purple-700 group {{ $link['active'] ? 'bg-purple-100 text-purple-700 font-semibold' : '' }}">
                                <span
                                    class="w-8 h-8 flex items-center justify-center text-gray-400 group-hover:text-purple-700 {{ $link['active'] ? 'text-purple-700' : '' }}">
                                    <i class="{{ $link['icon'] }} text-lg"></i>
                                </span>
                                <span class="ms-3">{{ $link['name'] }}</span>
                            </a>
                    @endif
                    </li>
                    @endforeach
                </ul>
            </div>
        </aside>

        <!-- FIX 2: mt-16 (64px) en lugar de mt-40 (160px) -->
        <div class="p-4 sm:ml-64 mt-16">
            <main>
                @include('partials.breadcrumbs')
                {{ $slot }}
            </main>
        </div>

        <script>
            @if(session('swal'))
                window.addEventListener('DOMContentLoaded', () => {
                    Swal.fire({
                        title: "{{ session('swal')['title'] }}",
                        text: "{{ session('swal')['text'] }}",
                        icon: "{{ session('swal')['icon'] }}"
                    });
                });
            @endif
        </script>
        {{-- FIX 3: Se eliminó el </div> huérfano que estaba aquí --}}

        @stack('modals')
        @livewireScripts
        <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            // Escuchadores de SweetAlert
            window.addEventListener('swal:modal', event => {
                Swal.fire({
                    title: event.detail[0].title,
                    text: event.detail[0].text,
                    icon: event.detail[0].icon
                });
            });
            window.addEventListener('swal:confirm', event => {
                Swal.fire({
                    title: event.detail[0].title,
                    text: event.detail[0].text,
                    icon: event.detail[0].icon,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch(event.detail[0].method, {
                            id: event.detail[0].id
                        });
                    }
                });
            });
        </script>
    </body>

    </html>
