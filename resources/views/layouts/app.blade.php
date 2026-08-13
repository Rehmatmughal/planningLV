<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Auth Role Demo</title>
    
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    {{-- <link href="{{ asset('css/all.min.css') }}" rel="stylesheet"> --}}


    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])


    {{-- <meta charset="UTF-8">
    <title>Auth Role Demo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
</head>
<body class="bg-gray-50">

<div class="flex">

    {{-- Sidebar --}}
    @include('layouts.sidebar')
        <div class="flex-1">
            <nav class="bg-white shadow p-4 flex justify-between">
                <strong>RBAC Panel</strong>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-red-600">Logout</button>
                </form>
            </nav>

            {{-- Main Content --}}
            <main class="p-6">
                @yield('content')
            </main>
        </div>   

</div>

</body>
</html>

