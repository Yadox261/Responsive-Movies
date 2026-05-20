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
            $displaySegments = [];
            $tempUrl = '';
            
            foreach($segments as $segment) {
                if ($segment == 'movies') {
                    $tempUrl = '/movies-admin';
                } elseif ($segment == 'users') {
                    $tempUrl = '/users-admin';
                } elseif ($segment == 'roles') {
                    $tempUrl = '/roles-admin';
                } else {
                    $tempUrl .= '/' . $segment;
                }

                if (is_numeric($segment)) {
                    continue;
                }

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
                    default => ucfirst($segment)
                };

                if ($segment != 'admin') {
                    $displaySegments[] = [
                        'name' => $name,
                        'url' => $tempUrl,
                    ];
                }
            }
        @endphp
        @foreach($displaySegments as $index => $item)
            @php
                $isLast = ($index == count($displaySegments) - 1);
            @endphp
            <li>
                <div class="flex items-center">
                    <i class="fa-solid fa-chevron-right text-gray-400 text-[10px] mx-2"></i>
                    @if($isLast)
                        <span class="text-sm font-bold text-red-600">{{ $item['name'] }}</span>
                    @else
                        <a href="{{ url($item['url']) }}" class="text-sm font-medium text-gray-700 hover:text-red-600">{{ $item['name'] }}</a>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>
</nav>
