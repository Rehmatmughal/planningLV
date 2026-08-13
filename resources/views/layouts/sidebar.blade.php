<aside class="w-64 bg-gray-100 min-h-screen p-4">
    <h2 class="text-lg font-bold mb-4">Menu</h2>

    <ul class="space-y-2">

        @foreach(config('sidebar') as $item)

            {{-- Agar permission null hai --}}
            @if(is_null($item['permission']))
                <li>
                    <a href="{{ route($item['route']) }}"
                       class="block p-2 rounded hover:bg-gray-200">
                        {{ $item['label'] }}
                    </a>
                </li>

            {{-- Agar permission defined hai --}}
            @else
                @can($item['permission'])
                    <li>
                        <a href="{{ route($item['route']) }}"
                           class="block p-2 rounded hover:bg-gray-200">
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endcan
            @endif

        @endforeach
        @can('activity.view')
        <li class="nav-item"> 
            <a class="nav-link" href="{{ route('activity.logs') }}"><i class="fas fa-vial"></i>Log</a>
        </li>
        @endcan

    </ul>
</aside>

{{-- 
<aside class="w-64 bg-white shadow h-screen p-4">
    <ul class="space-y-2">

        <li>
            <a href="{{ route('dashboard') }}"
               class="block p-2 rounded hover:bg-gray-200">
                Dashboard
            </a>
        </li>

        @can('user.view')
        <li>
            <a href="{{ route('admin.users.index') }}"
               class="block p-2 rounded hover:bg-gray-200">
                Users
            </a>
        </li>
        @endcan

        @can('role.view')
        <li>
            <a href="{{ route('admin.roles.index') }}"
               class="block p-2 rounded hover:bg-gray-200">
                Roles
            </a>
        </li>
        @endcan

        @can('permission.view')
        <li>
            <a href="{{ route('admin.permissions.index') }}"
               class="block p-2 rounded hover:bg-gray-200">
                Permissions
            </a>
        </li>
        @endcan

    </ul>
</aside>
 --}}
