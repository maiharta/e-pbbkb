@extends('layouts.auth-base', ['title' => 'Login'])

@section('content')
    <div class="mb-4">
        <x-logo />
    </div>
    <h1 class="title">Login</h1>
    <p class="subtitle">Masukkan email dan password untuk masuk ke akun anda</p>
    <form action="#"
          class="needs-validation"
          method="POST">
        @csrf
        <x-input-group :autofocus=true
                       :required=true
                       icon="user"
                       name="email"
                       placeholder="Email"
                       type="email" />
        <x-input-group :required=true
                       icon="lock"
                       name="password"
                       placeholder="Password"
                       type="password" />
        <a class="forgot-password d-block mb-2"
           href="{{ route('password.request') }}">
            Lupa Password?
        </a>
        <div class="form-group mb-4">
            {!! htmlFormSnippet() !!}
        </div>
        <button class="btn w-100 shadow""
                type="submit">
            Login
        </button>
        <p class="text-center mt-3 footer-caption">
            Belum punya akun? <a class="footer-link"
               href="{{ route('register.index') }}">Register</a>
        </p>
    </form>
@endsection
