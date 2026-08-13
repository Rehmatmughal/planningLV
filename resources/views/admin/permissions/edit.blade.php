@extends('layouts.app')

@section('content')

<h2 class="text-xl font-bold mb-4">Edit Permission</h2>

<form method="POST"
      action="{{ route('admin.permissions.update', $permission) }}"
      class="bg-white p-4 rounded shadow w-1/2">
    @csrf
    @method('PUT')

    <div class="mb-4">
        <label class="block mb-1">Permission Name</label>
        <input type="text"
               name="name"
               value="{{ $permission->name }}"
               class="border p-2 w-full"
               required>
    </div>

    <button class="bg-blue-600 text-white px-4 py-2 rounded">
        Update
    </button>

</form>

@endsection
