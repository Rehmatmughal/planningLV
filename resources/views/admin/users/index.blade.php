@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
 
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

<h2 class="text-xl font-bold mb-3">Users</h2>
@can('user.create')
<a href="{{ route('admin.users.create') }}"
   class="bg-blue-600 text-white px-3 py-2 rounded">
    Create User
</a>
@endcan
 
<table class="w-full mt-4 bg-white shadow rounded">
    <thead>
        <tr class="border-b">
            <th class="p-2">Name</th>
            <th class="p-2">Email</th>
            <th class="p-2">Role</th>
            <th class="p-2">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr class="border-b">
            <td class="p-2">{{ $user->name }}</td>
            <td class="p-2">{{ $user->email }}</td>
            <td class="p-2">
                {{ $user->getRoleNames()->first() }}
            </td>
            
            <td class="p-2">
                <a href="{{ route('admin.users.edit',$user) }}"
                   class="text-blue-600">Edit</a>
                   @can('user.delete')
                   <form action="{{ route('admin.users.destroy', $user) }}"
                        method="POST"
                        class="inline"
                        onsubmit="return confirm('Are you sure you want to delete this user?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600">
                            Delete
                        </button>
                    </form>
                    @endcan 

            </td>

        </tr> 
    @endforeach
    </tbody>

</table>
<h2 class="text-xl font-bold mt-8 mb-3">My Permissions</h2>

<table class="w-full bg-white shadow rounded">
    <thead>
        <tr class="border-b bg-gray-100">
            <th class="p-2 text-left">Permission</th>
            <th class="p-2 text-center">Yes</th>
            <th class="p-2 text-center">No</th>
        </tr>
    </thead>

    <tbody>
        @foreach($permissions as $permission)
        <tr class="border-b">
            <td class="p-2">{{ $permission->name }}</td>

            {{-- YES column --}}
            <td class="p-2 text-center">
                {{-- @can($permission) --}}
                @can($permission->name)
                    ✅
                @endcan
            </td>

            {{-- NO column --}}
            <td class="p-2 text-center">
                @cannot($permission->name)
                {{-- @cannot($permission) --}}
                    ❌
                @endcannot
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
