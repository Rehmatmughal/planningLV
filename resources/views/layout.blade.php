<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Management System</title>

    <!-- Bootstrap 5 CSS -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- FontAwesome -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> -->
    <link href="{{ asset('css/all.min.css') }}" rel="stylesheet">


    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
    <style>
        nav .w-5{
            display:none;
        }
            

    </style>

    <style>
        /* Make sure body takes full height */
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        /* Main content should take available space */
        .content-wrapper {
            flex: 1;
        }

        /* Footer styling */
        footer {
            background-color: #343a40;
            color: white;
            padding: 15px 0;
            text-align: center;
        }

        .navbar-brand img {
            max-height: 40px;
        }

        .admin-nav .nav-link {
            color: white !important;
            font-weight: 500;
        }

        .admin-nav .nav-link:hover {
            color: #ffc107 !important;
        }
    </style>
</head>
<body>

    @include('header')
    @yield('content')
    @include('footer')
    @yield('scripts')

    {{-- @if (!Request::is('/login')) 
        @include('partials.header')
    @endif

    <div class="content-wrapper">
        @yield('content')
    </div>
    @if (!Request::is('/login'))
        @include('partials.footer')
    @endif --}}

    @stack('scripts')
    <!-- ✅ Add this before closing body tag -->
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    

</body>
</html>
