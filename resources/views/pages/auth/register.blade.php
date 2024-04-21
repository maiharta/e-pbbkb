@extends('layouts.auth-base', ['title' => 'Register'])

@section('content')
    <div class="mb-4">
        <x-logo />
    </div>
    <h1 class="title">Register</h1>
    <p class="subtitle">Masukkan email anda untuk mendaftar akun</p>
    <input id="route_otp"
           type="hidden"
           value="{{ route('register.otp.generate') }}">
    <input id="route_register"
           type="hidden"
           value="{{ route('register.store') }}">
    <input id="csrf_token"
           type="hidden"
           value="{{ csrf_token() }}">
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

    <button class="btn btn-primary w-100"
            id="generate_otp"
            type="button">
        Daftar
    </button>
    <p class="text-center mt-3 footer-caption">
        Sudah punya akun? <a class="footer-link"
           href="{{ route('login.index') }}">Login</a>
    </p>

    <!-- Modal -->
    <div aria-hidden="true"
         aria-labelledby="staticBackdropLabel"
         class="modal fade bg-transparent"
         data-bs-backdrop="static"
         data-bs-keyboard="false"
         id="staticBackdrop"
         tabindex="-1">
        <div class="modal-dialog modal-dialog-centered bg-transparent">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 fw-bold"
                        id="staticBackdropLabel">Verifikasi Email</h1>
                    <button aria-label="Close"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            type="button"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Masukkan kode otp yang dikirimkan ke email anda. Jangan bagikan kode OTP anda kepada
                        siapapun.</p>
                    <div class="mb-3 mt-3">
                        <x-otp-input />
                    </div>
                    <p class="text-center mb-0"
                       id="countdown-container">(OTP Terakhir <span id="last_otp_at"></span>) Anda dapat melakukan kirim
                        ulang kode
                        OTP setelah <span class="fw-bold"
                              id="countdown">-:-</span></p>
                    <p class="text-center mb-0 d-none"
                       id="resend-otp-container">Tidak mendapatkan Kode Verifikasi? <button
                                class="fw-bold text-decoration-none text-primary bg-transparent border-0"
                                href="#"
                                id="resend-otp">Kirim Ulang</button></p>
                </div>
                <div class="modal-footer">
                    <button id="register" class="btn btn-primary w-100"
                            type="button">Verifikasi</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@vite(['resources/js/auth/register.js'])
