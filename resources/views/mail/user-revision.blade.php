<x-mail::message>
# Permintaan Revisi Data Akun

Halo **{{ $user->name }}**,

Kami telah meninjau pendaftaran akun Anda dan menemukan beberapa hal yang perlu diperbaiki.

## Catatan Revisi:

<x-mail::panel>
{{ $catatanRevisi }}
</x-mail::panel>

Mohon untuk melakukan perbaikan sesuai dengan catatan di atas dan melengkapi kembali data Anda.

<x-mail::button :url="url('/')">
Perbaiki Data Sekarang
</x-mail::button>

Setelah Anda melakukan perbaikan, data Anda akan ditinjau kembali oleh tim kami.

Jika Anda memiliki pertanyaan atau memerlukan klarifikasi lebih lanjut, jangan ragu untuk menghubungi kami.

Terima kasih,<br>
<strong>{{ config('app.name') }}</strong>
</x-mail::message>
