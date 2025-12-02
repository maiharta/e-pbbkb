<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0"
          name="viewport">
    <meta content="ie=edge"
          http-equiv="X-UA-Compatible">
    <title>{{ config('app.name') }}</title>
    <link href="{{ asset('assets/images/favicon.png') }}"
          rel="shortcut icon"
          type="image/x-icon">
    @vite(['resources/scss/app.scss', 'resources/scss/themes/dark/app-dark.scss'])
    <link href="{{ asset('assets/vendors/iconsax/style.css') }}"
          rel="stylesheet">

    {{-- datatables bootstrap --}}
    <link href="{{ asset('assets/vendors/datatables/dataTables.bootstrap5.min.css') }}"
          rel="stylesheet">
    {{-- datatables rowreorder --}}
    <link href="{{ asset('assets/vendors/datatables/rowReorder.dataTables.min.css') }}"
          rel="stylesheet">
    {{-- datatables button --}}
    <link href="{{ asset('assets/vendors/datatables/buttons.bootstrap5.min.css') }}"
          rel="stylesheet">
    {{-- select 2 --}}
    <link href="{{ asset('assets/vendors/select2/select2.min.css') }}"
          rel="stylesheet">
    {{-- daterangepicker --}}
    <link href="{{ asset('assets/vendors/daterangepicker/daterangepicker.css') }}"
          rel="stylesheet">

    @stack('styles')
</head>

<body>
    <script src="{{ asset('assets/js/initTheme.js') }}"></script>
    <div class="loader-container">
        <div class="loader"></div>
    </div>
    <div id="app">
        <div id="sidebar">
            @include('layouts.partials.sidebar')
        </div>
        <div class='layout-navbar navbar-fixed'
             id="main">
            @include('layouts.partials.header')
            <div id="main-content">
                @yield('content')
            </div>
            @include('layouts.partials.footer')
        </div>
    </div>
    <script src="{{ asset('assets/js/components/dark.js') }}"></script>
    {{-- perfect scrollbar cdn --}}
    <script src="{{ asset('assets/js/perfect-scrollbar.min.js') }}"></script>
    <!-- or, you may also directly use a CDN :-->
    <script src="{{ asset('assets/js/autonumeric.js') }}"></script>
    {{-- jquery --}}
    <script src="{{ asset('assets/vendors/jquery/jquery-3.4.1.min.js') }}"></script>
    {{-- sweet alert 2 --}}
    <script src="{{ asset('assets/vendors/sweetalert2/sweetalert2@11.js') }}"></script>
    {{-- datatables --}}
    <script src="{{ asset('assets/vendors/datatables/dataTables.min.js') }}"></script>
    {{-- datatables bootstrap --}}
    <script src="{{ asset('assets/vendors/datatables/dataTables.bootstrap5.min.js') }}"></script>
    {{-- datatables rowreorder --}}
    <script src="{{ asset('assets/vendors/datatables/dataTables.rowReorder.min.js') }}"></script>
    {{-- datatables button --}}
    <script src="{{ asset('assets/vendors/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/datatables/buttons.bootstrap5.min.js') }}"></script>
    {{-- select 2 --}}
    <script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
    {{-- moment --}}
    <script src="{{ asset('assets/vendors/moment/moment.min.js') }}"></script>
    {{-- dateragepicker --}}
    <script src="{{ asset('assets/vendors/daterangepicker/daterangepicker.min.js') }}"></script>
    {{-- flatpickr --}}
    <script src="{{ asset('assets/vendors/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/flatpickr/id.js') }}"></script>
    {{-- @yield('content') --}}
    @vite(['resources/js/app.js'])
    @stack('scripts')
    @include('components.toastr')

    {{-- Prevent back navigation after logout --}}
    <script>
        // Disable back button functionality
        window.addEventListener('load', function() {
            // Push a new state to prevent back navigation
            window.history.pushState(null, null, window.location.href);
            
            // Listen for popstate event (back button)
            window.onpopstate = function() {
                // Push state again to prevent going back
                window.history.pushState(null, null, window.location.href);
            };
        });

        // Additional security: Clear browser cache on page unload
        window.addEventListener('beforeunload', function() {
            // Clear any sensitive data from memory
            if (typeof(Storage) !== "undefined") {
                sessionStorage.clear();
            }
        });

        // Prevent right-click context menu (optional security measure)
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        // Prevent common keyboard shortcuts for developer tools (optional)
        document.addEventListener('keydown', function(e) {
            // Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
            if (e.keyCode === 123 || 
                (e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74)) ||
                (e.ctrlKey && e.keyCode === 85)) {
                e.preventDefault();
                return false;
            }
        });
    </script>

</body>

</html>
