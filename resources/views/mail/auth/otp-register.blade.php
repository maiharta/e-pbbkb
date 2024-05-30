<x-mail::message>
# Verifikasi Email

Berikut adalah kode OTP untuk verifikasi email Anda. Jangan berikan kode ini kepada siapapun.

<x-mail::panel>
<strong>{{ $otp }}</strong>
</x-mail::panel>

Jika Anda tidak merasa melakukan tindakan ini, abaikan email ini.<br>

Terima kasih,<br>
<strong>{{ config('app.name') }}</strong>
</x-mail::message>
