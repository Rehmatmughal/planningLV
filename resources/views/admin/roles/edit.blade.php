@extends('layouts.app')

@section('content')

<h2 class="text-xl font-bold mb-4">
    Edit Role: {{ $role->name }}
</h2>

<form method="POST"
      action="{{ route('admin.roles.update',$role) }}">
@csrf
@method('PUT')

<input name="name"
       value="{{ $role->name }}"
       class="border p-2 w-full mb-3">

<h4 class="font-semibold mb-2">Permissions</h4>

@foreach($permissions as $permission)
<label class="block">
    <input type="checkbox"
           name="permissions[]"
           value="{{ $permission->name }}"
           {{ in_array($permission->name,$rolePermissions) ? 'checked' : '' }}>
    {{ $permission->name }}
</label>
@endforeach

<button class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">
    Update
</button>

</form>

@endsection
