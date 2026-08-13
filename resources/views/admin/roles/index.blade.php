@extends('layouts.app')

@section('content')

<h2 class="text-xl font-bold mb-4">Roles</h2>

<a href="{{ route('admin.roles.create') }}"
   class="bg-blue-600 text-white px-3 py-2 rounded">
   Create Role
</a>

<table class="mt-4 bg-white w-full shadow rounded">
    <thead>
        <tr class="border-b bg-gray-100">
            <th class="p-2 text-left">Role Name</th>
            <th class="p-2 text-left">Actions</th>
        </tr>
    </thead>

    <tbody>
    @foreach($roles as $role)
        <tr class="border-b"> 
            <td class="p-2">{{ $role->name }}</td>

            <td class="p-2">
                @can('role.edit')
                <a href="{{ route('admin.roles.edit', $role->id) }}"
                   {{-- class="bg-yellow-500 text-white px-2 py-1 rounded text-sm"> --}}
                   class="px-2 py-1 rounded text-sm">
                    Edit
                </a>
                @endcan
                @can('role.delete')
                {{-- <form method="POST" ...>
                <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this role?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger mb-1">Delete</button>
                </form> --}}
                <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>

                @endcan

            </td>
        </tr>
    @endforeach
    </tbody>
</table>

@endsection

{{-- @extends('layouts.app')

@section('content')

<h2 class="text-xl font-bold mb-4">Roles</h2>

<a href="{{ route('admin.roles.create') }}"
   class="bg-blue-600 text-white px-3 py-2 rounded">
   Create Role
</a>

<table class="mt-4 bg-white w-full shadow rounded">
@foreach($roles as $role)
<tr class="border-b">
    <td class="p-2">{{ $role->name }}</td>
</tr>
@endforeach
</table>

@endsection --}}
