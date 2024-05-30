@props([
    'title' => '',
])

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }}{{ $title ? ' - ' . $title : '' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    {{-- nunito --}}
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    {{-- VENDORS --}}
    <link rel="stylesheet" href="{{ asset('assets/vendors/iconsax/style.css') }}">
    {{-- favicon --}}
    <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/png">
    {{-- custom css --}}
    @stack('styles')
    @vite(['resources/scss/auth/style.scss'])
</head>

<body>
    {{-- left 40%, right is bg with 60%, height 100vh --}}
    <div class="container-fluid h-100 px-0">
        <div class="row align-items-center justify-content-center h-100 h-md-auto">
            <div class="col-auto col-md-5 px-5">
                @yield('content')
            </div>
            <div class="d-none d-md-block col-7 hero">
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    {{-- Jquery --}}
    <script src="{{ asset('assets/js/jquery-3.4.1.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/vendors/sweetalert2/sweetalert2@11.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/js/toastr.min.js') }}"></script> --}}
    <x-toastr/>
    @vite(['resources/js/app.js', 'resources/js/auth.js'])
    @stack('scripts')
</body>

</html>
