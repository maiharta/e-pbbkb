<x-mail::message>
# Pelaporan Baru Menunggu Verifikasi

Halo Admin,

Terdapat pelaporan baru telah dikirimkan oleh operator dan menunggu verifikasi Anda.

## Detail Pelaporan:

<x-mail::panel>
**Operator:** {{ $pelaporan->user->name }}

**Email Operator:** {{ $pelaporan->user->email }}

**Periode:** {{ $pelaporan->bulan_name }} {{ $pelaporan->tahun }}

**Tanggal Pengiriman:** {{ $pelaporan->first_send_at ? \Carbon\Carbon::parse($pelaporan->first_send_at)->format('d M Y H:i') : now()->format('d M Y H:i') }}
</x-mail::panel>

Silakan lakukan verifikasi pelaporan ini segera.

<x-mail::button :url="url('/admin/verifikasi/pelaporan')">
Verifikasi Sekarang
</x-mail::button>

Terima kasih,<br>
<strong>{{ config('app.name') }}</strong>
</x-mail::message>
