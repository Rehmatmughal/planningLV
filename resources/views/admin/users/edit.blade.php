@extends('layouts.app')

@section('content')
<div class="container">
<form method="POST" action="{{ route('admin.users.update',$user) }}">
@csrf @method('PUT')

<input type="text" name="name" value="{{ $user->name }}" class="form-control mb-2">
<input type="email" name="email" value="{{ $user->email }}" class="form-control mb-2">

<select name="role" class="form-control mb-2">
    @foreach($roles as $role)
        @if (
            auth()->user()->hasRole('super-admin')
            || !in_array($role, ['admin', 'super-admin'])
        )
            <option value="{{ $role }}"
                {{ $user->hasRole($role) ? 'selected' : '' }}>
                {{ ucwords(str_replace('-', ' ', $role)) }}
            </option>
        @endif
    @endforeach
</select>
{{-- <select name="role" class="form-control mb-2">
@foreach($roles as $role)
    <option value="{{ $role }}"
        {{ $user->hasRole($role) ? 'selected' : '' }}>
        {{ $role }}
    </option>
@endforeach
</select> --}}

<button class="btn btn-success">Update</button>
</form>
</div>
@endsection
