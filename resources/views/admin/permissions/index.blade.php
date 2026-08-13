@extends('layouts.app')

@section('content')

<h2 class="text-xl font-bold mb-4">Permissions</h2>

@can('permission.create')
<a href="{{ route('admin.permissions.create') }}"
   class="bg-blue-600 text-white px-3 py-2 rounded">
   Create Permission
</a>
@endcan

<table class="mt-4 bg-white w-full shadow rounded">
    <thead>
        <tr class="bg-gray-100">
            <th class="p-2 text-left">Name</th>
            <th class="p-2 text-right">Actions</th>
        </tr>
    </thead>
    <tbody>
    @foreach($permissions as $permission)
        <tr class="border-b">
            <td class="p-2">{{ $permission->name }}</td>
            <td class="p-2 text-right space-x-2">

                @can('permission.edit')
                <a href="{{ route('admin.permissions.edit', $permission) }}"
                   class="text-blue-600">Edit</a>
                @endcan

                @can('permission.delete')
                <form action="{{ route('admin.permissions.destroy', $permission) }}"
                      method="POST"
                      class="inline"
                      onsubmit="return confirm('Delete permission?')">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-600">Delete</button>
                </form>
                @endcan

            </td>
        </tr>
    @endforeach
    </tbody>
</table>

@endsection
