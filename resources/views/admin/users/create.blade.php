@extends('layouts.app')

@section('content')
<div class="container">
<form method="POST" action="{{ route('admin.users.store') }}">
@csrf

<input type="text" name="name" class="form-control mb-2" placeholder="Name">
<input type="email" name="email" class="form-control mb-2" placeholder="Email">
<input type="password" name="password"
       placeholder="Password"
       class="border p-2 w-full mb-3">

<select name="role" class="form-control mb-2">
    @foreach($roles as $role)
        <option value="{{ $role->name }}">{{ $role->name }}</option>
    @endforeach
</select>

<button class="btn btn-primary">Save</button>
</form>
</div>
@endsection
