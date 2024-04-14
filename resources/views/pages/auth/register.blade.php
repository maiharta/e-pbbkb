@extends('layouts.auth-base', ['title' => 'Register'])

@section('content')
    <div class="mb-4">
        <x-logo />
    </div>
    <h1 class="title">Register</h1>
    <p class="subtitle">Masukkan email anda untuk mendaftar akun</p>
    <x-input-group :required=true
                   icon="sms"
                   name="email"
                   placeholder="Email"
                   type="email" />
    <x-input-group :required=true
                   icon="lock"
                   name="password"
                   placeholder="Password"
                   type="password" />
    <x-input-group :required=true
                   icon="lock"
                   name="password_verify"
                   placeholder="Konfirmasi Password"
                   type="password" />
    <button class="btn w-100 shadow""
            type="submit">
        Daftar
    </button>
    <p class="text-center mt-3 footer-caption">
        Sudah punya akun? <a class="footer-link"
           href="#">Login</a>
    </p>
@endsection
