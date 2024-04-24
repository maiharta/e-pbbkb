<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }}</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">
    @vite(['resources/scss/app.scss', 'resources/scss/themes/dark/app-dark.scss'])
    <link rel="stylesheet" href="{{ asset('assets/vendors/iconsax/style.css') }}">

    {{-- datatables bootstrap --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/datatables/dataTables.bootstrap5.min.css') }}">
    {{-- datatables rowreorder --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/datatables/rowReorder.dataTables.min.css') }}">
    {{-- datatables button --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/datatables/buttons.bootstrap5.min.css') }}">
    {{-- select 2 --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
    {{-- daterangepicker --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/daterangepicker/daterangepicker.css') }}">

    @stack('styles')
</head>

<body>
    <script src="{{ asset('assets/js/initTheme.js') }}"></script>
    <div id="app">
        <div id="sidebar">
            @include('layouts.partials.sidebar')
        </div>
        <div id="main" class='layout-navbar navbar-fixed'>
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
    {{-- @yield('content') --}}
    @vite(['resources/js/app.js'])
    @stack('scripts')
    @include('components.toastr')

</body>

</html>
