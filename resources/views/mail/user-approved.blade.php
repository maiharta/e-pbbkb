<x-mail::message>
# Selamat! Akun Anda Telah Diverifikasi

Halo **{{ $user->name }}**,

Kami dengan senang hati memberitahukan bahwa akun Anda telah berhasil diverifikasi dan disetujui oleh admin.

<x-mail::panel>
Anda sekarang dapat mengakses semua fitur yang tersedia di sistem kami.
</x-mail::panel>

<x-mail::button :url="url('/')">
Login Sekarang
</x-mail::button>

Jika Anda memiliki pertanyaan atau memerlukan bantuan, jangan ragu untuk menghubungi kami.

Terima kasih,<br>
<strong>{{ config('app.name') }}</strong>
</x-mail::message>
