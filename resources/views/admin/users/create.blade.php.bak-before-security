@extends('layouts.app')

@section('content')
<div class="container">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
 
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
<form method="POST" action="{{ route('admin.users.store') }}">
@csrf

<input type="text" name="name" value="{{ old('name')}}" class="form-control mb-2" placeholder="Name">
@error('name')
    <div class="text-danger mb-2">{{ $message }}</div>
@enderror
<input type="email" name="email" value="{{ old('email')}}" class="form-control mb-2" placeholder="Email">
@error('email')
    <div class="text-danger mb-2">{{ $message }}</div>
@enderror

<input type="password" name="password"
       placeholder="Password"
       class="border p-2 w-full mb-3">

@error('password')
    <div class="text-danger mb-2">{{ $message }}</div>
@enderror

<input type="password" name="password_confirmation"
       placeholder="Confirm Password"
       class="border p-2 w-full mb-3">

@error('password')
    <div class="text-danger mb-2">{{ $message }}</div>
@enderror

<select name="role" class="form-control mb-2">
    @foreach($roles as $role)
        <option value="{{ $role->name }}">{{ $role->name }}</option>
    @endforeach
</select>

<button class="btn btn-primary">Save</button>
</form>
</div>
@endsection
