<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }}</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.ico') }}" type="image/x-icon">
    @vite(['resources/scss/app.scss', 'resources/scss/themes/dark/app-dark.scss'])
    <link rel="stylesheet" href="{{ asset('assets/vendors/iconsax/style.css') }}">
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
    <!-- ...or, you may also directly use a CDN :-->
    <script src="{{ asset('assets/js/autonumeric.js') }}"></script>
    {{-- @yield('content') --}}
    @vite(['resources/js/app.js'])
    @stack('scripts')
    @include('components.toastr')

</body>

</html>
