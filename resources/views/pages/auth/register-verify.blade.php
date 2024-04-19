@extends('layouts.auth-base', ['title' => 'Verifikasi Email'])

@section('content')
    <div class="mb-4">
        <x-logo />
    </div>
    <h1 class="title">Verifikasi Email</h1>
    <p class="subtitle mb-2">Masukkan kode otp yang dikirimkan ke email anda</p>
    <p class="mb-2 mt-0 p-0text-primary">aangpangantyas@gmail.com <a href="#" class="text-primary text-decoration-underline">ganti email?</a></p>
    <x-otp-input />
    <p class="text-center sisa-waktu">Sisa Waktu : <span class="fw-bold"></span></p>
    <button class="btn w-100 shadow"
            id="send-form"
            type="submit">
        Verifikasi
    </button>
    <button id="resend-otp" class="border-0 text-center w-100 d-block mt-3 footer-link">Kirim Ulang Kode OTP</button>
@endsection

@push('scripts')
    {{-- <script>
        var countDownDate = new Date().getTime() + 5000;
        let countDownInterval = setInterval(function() {
            let now = new Date().getTime();
            let distance = countDownDate - now;
            let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            let seconds = Math.floor((distance % (1000 * 60)) / 1000);
            document.querySelector('.sisa-waktu span').innerText = `${minutes}:${seconds}`;
            if (distance < 0) {
                clearInterval(countDownInterval);
                document.querySelector('.sisa-waktu span').innerText = '0:00';
                $('#send-form').attr('disabled', true);
            }
        }, 1000);

        $('#resend-otp').click(function() {
            $('#send-form').attr('disabled', false);
            countDownDate = new Date().getTime() + 10000;
            countDownInterval = setInterval(function() {
                let now = new Date().getTime();
                let distance = countDownDate - now;
                let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                let seconds = Math.floor((distance % (1000 * 60)) / 1000);
                document.querySelector('.sisa-waktu span').innerText = `${minutes}:${seconds}`;
                if (distance < 0) {
                    clearInterval(countDownInterval);
                    document.querySelector('.sisa-waktu span').innerText = '0:00';
                    $('#send-form').attr('disabled', true);
                }
            }, 1000);
        });
    </script> --}}
@endpush
