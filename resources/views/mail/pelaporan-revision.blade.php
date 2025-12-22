<x-mail::message>
# Permintaan Revisi Pelaporan

Halo **{{ $pelaporan->user->name }}**,

Kami telah meninjau pelaporan Anda dan menemukan beberapa hal yang perlu diperbaiki.

**Periode Pelaporan:** {{ $pelaporan->periode_formatted ?? $pelaporan->periode }}

## Catatan Revisi:

<x-mail::panel>
{{ $catatanRevisi }}
</x-mail::panel>

Mohon untuk melakukan perbaikan sesuai dengan catatan di atas dan mengirimkan kembali pelaporan Anda.

<x-mail::button :url="url('/')">
Perbaiki Pelaporan Sekarang
</x-mail::button>

Setelah Anda melakukan perbaikan, pelaporan Anda akan ditinjau kembali oleh tim kami.

Jika Anda memiliki pertanyaan atau memerlukan klarifikasi lebih lanjut, jangan ragu untuk menghubungi kami.

Terima kasih,<br>
<strong>{{ config('app.name') }}</strong>
</x-mail::message>
