@extends('layouts.app')

@section('content')

<h2 class="text-xl font-bold mb-4">Create Permission</h2>

<form method="POST" action="{{ route('admin.permissions.store') }}"
      class="bg-white p-4 rounded shadow w-1/2">
    @csrf

    <div class="mb-4">
        <label class="block mb-1">Permission Name</label>
        <input type="text"
               name="name"
               class="border p-2 w-full"
               placeholder="e.g user.create"
               required>
    </div>

    <button class="bg-blue-600 text-white px-4 py-2 rounded">
        Save
    </button>

</form>

@endsection
