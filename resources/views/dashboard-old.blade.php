@extends('layouts.app')

@section('content')

<h2 class="text-2xl font-bold mb-4">
    Dashboard
</h2>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">

    <div class="bg-white p-4 shadow rounded">
        <h4 class="font-bold">User</h4>
        <p>{{ auth()->user()->name }}</p>
    </div>

    <div class="bg-white p-4 shadow rounded">
        <h4 class="font-bold">Role</h4>
        <p>{{ auth()->user()->getRoleNames()->join(', ') }}</p>
    </div>

    @role('admin|super-admin')
    <div class="bg-white p-4 shadow rounded">
        <h4 class="font-bold">Admin</h4>
        <a href="{{ route('admin.users.index') }}"
           class="text-blue-600 underline">
            Manage Users
        </a>
    </div>
    @endrole

</div>

@endsection
