<x-mail::message>
# Selamat! Pelaporan Anda Telah Diverifikasi

Halo **{{ $pelaporan->user->name }}**,

Kami dengan senang hati memberitahukan bahwa pelaporan Anda telah berhasil diverifikasi dan disetujui oleh admin.

<x-mail::panel>
**Periode Pelaporan:** {{ $pelaporan->bulan_name }} {{ $pelaporan->tahun }}

**Status:** Terverifikasi ✓

Anda bisa melanjutkan ke tahap generate SPTPD
</x-mail::panel>

<x-mail::button :url="url('/')">
Akses Dashboard
</x-mail::button>

Jika Anda memiliki pertanyaan atau memerlukan bantuan, jangan ragu untuk menghubungi kami.

Terima kasih,<br>
<strong>{{ config('app.name') }}</strong>
</x-mail::message>
