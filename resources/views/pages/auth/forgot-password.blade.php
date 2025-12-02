@extends('layouts.auth-base', ['title' => 'Login'])

@section('content')
    <div class="mb-4">
        <x-logo />
    </div>
    <h1 class="title">Lupa Password</h1>
    <p class="subtitle">Masukkan email anda untuk mereset password</p>
    <form action="{{ route('password.email') }}"
          method="POST">
        @csrf
        <x-input-group :autofocus=true
                       :required=true
                       icon="user"
                       name="email"
                       placeholder="Email"
                       type="email" />
        <button class="btn w-100 shadow""
                type="submit">
            Send Link Reset Password
        </button>
        <p class="text-center mt-3 footer-caption">
            Belum punya akun? <a class="footer-link"
               href="{{ route('register.index') }}">Register</a>
        </p>
    </form>
@endsection
