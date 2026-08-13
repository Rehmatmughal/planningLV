<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    {{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    {{-- <style>
        nav     .w-5{
            display: none;
        }
        
    </style> --}}


</head>
<body>

<div class="d-flex">    

    {{-- Sidebar --}}
    @include('partials.sidebar')

    {{-- Main Content --}}
    <div class="flex-grow-1 p-4">
        @include('header')
        @yield('content')
        @include('footer')
        
    </div>

</div>
@yield('scripts')
@stack('scripts')
{{-- <script src="{{ asset('js/app.js') }}"></script> --}}
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>
