<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="School Management System Dashboard">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - School Management System</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    <link rel="stylesheet" href="{{ asset('assets/vendors/core/core.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/demo1/style.css') }}">
    
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendors/flatpickr/flatpickr.min.css') }}" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="{{ asset('assets/vendors/datatables.net-bs5/dataTables.bootstrap5.css') }}" media="print" onload="this.media='all'">

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />

    <script src="{{ asset('assets/js/color-modes.js') }}"></script>
</head>
<body>
    <div class="main-wrapper">
        @include('partials.app-sidebar')

        <div class="page-wrapper">
            @include('partials.app-header')

            <div class="page-content">
                @yield('content')
            </div>

            @include('partials.footer')
        </div>
    </div>

    <script src="{{ asset('assets/vendors/core/core.js') }}" defer></script>
    
    <script src="{{ asset('assets/vendors/jquery/jquery.min.js') }}" defer></script>
    <script src="{{ asset('assets/vendors/datatables.net/dataTables.js') }}" defer></script>
    <script src="{{ asset('assets/vendors/datatables.net-bs5/dataTables.bootstrap5.js') }}" defer></script>
    <script src="{{ asset('assets/vendors/flatpickr/flatpickr.min.js') }}" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest/dist/apexcharts.min.js" defer></script>
    
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js" defer></script>

    <script src="{{ asset('assets/js/app.js') }}" defer></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Lucide icons once
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>