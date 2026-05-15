<nav class="flex mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-red-600">
                <i class="fa-solid fa-house mr-2 text-xs"></i>
                Admin
            </a>
        </li>
        @php
            $segments = request()->segments();
            $url = '';
        @endphp
        @foreach($segments as $index => $segment)
            @php
                $url .= '/' . $segment;
                $isLast = ($index == count($segments) - 1);
                $name = match($segment) {
                    'admin' => 'Panel',
                    'movies-admin' => 'Películas',
                    'users-admin' => 'Usuarios',
                    'roles-admin' => 'Roles',
                    'create' => 'Nuevo',
                    'edit' => 'Editar',
                    'movies' => 'Películas',
                    'users' => 'Usuarios',
                    'roles' => 'Roles',
                    default => is_numeric($segment) ? '#' . $segment : ucfirst($segment)
                };
            @endphp
            @if($segment != 'admin')
                <li>
                    <div class="flex items-center">
                        <i class="fa-solid fa-chevron-right text-gray-400 text-[10px] mx-2"></i>
                        @if($isLast)
                            <span class="text-sm font-bold text-red-600">{{ $name }}</span>
                        @else
                            <a href="{{ url($url) }}" class="text-sm font-medium text-gray-700 hover:text-red-600">{{ $name }}</a>
                        @endif
                    </div>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
