@extends('layouts.auth-base', ['title' => 'Login'])

@section('content')
    <div class="mb-4">
        <x-logo />
    </div>
    <h1 class="title">Lupa Password</h1>
    <p class="subtitle">Masukkan password baru anda</p>
    <form action="{{ route('password.update') }}"
          method="POST">
        @csrf
        <input name="token"
               type="hidden"
               value="{{ $token }}" />
        <x-input-group :readonly=true
                       :required=true
                       icon="sms"
                       name="email"
                       placeholder="Email"
                       type="email"
                       value="{{ $email }}" />
        <div id="password-validation">
            <x-input-group :required=true
                           icon="lock"
                           name="password"
                           placeholder="Password"
                           type="password" />
        </div>
        <x-input-group :required=true
                       icon="lock"
                       name="password_confirmation"
                       placeholder="Konfirmasi Password"
                       type="password" />
        <button class="btn w-100 shadow""
                type="submit">
            Reset Password
        </button>
    </form>
@endsection
