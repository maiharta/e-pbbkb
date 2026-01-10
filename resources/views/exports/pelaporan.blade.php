<table>
    <tr>
        <th colspan="10"
            style="text-align: center;font-weight: bold;">LAPORAN DATA PELAPORAN</th>
    </tr>
    @if($periode_awal || $periode_akhir)
    <tr>
        <th colspan="10"
            style="text-align: center;">
            Periode:
            @if($periode_awal)
                @php
                    list($startMonth, $startYear) = explode('-', $periode_awal);
                    echo date('F Y', mktime(0, 0, 0, $startMonth, 1, $startYear));
                @endphp
            @endif
            @if($periode_awal && $periode_akhir)
                s/d
            @endif
            @if($periode_akhir)
                @php
                    list($endMonth, $endYear) = explode('-', $periode_akhir);
                    echo date('F Y', mktime(0, 0, 0, $endMonth, 1, $endYear));
                @endphp
            @endif
        </th>
    </tr>
    @endif
    @if($status)
    <tr>
        <th colspan="10"
            style="text-align: center;">
            Status: {{ $status === 'verified' ? 'Terverifikasi' : 'Berjalan' }}
        </th>
    </tr>
    @endif
    <tr></tr>
</table>
<table border="1"
       cellpadding="5"
       cellspacing="0"
       style="border-collapse: collapse;">
    <tr>
        <th style="font-weight: bold; text-align: center;border: 1px solid #000000;">No</th>
        <th style="font-weight: bold; text-align: center;border: 1px solid #000000;">Nama Perusahaan</th>
        <th style="font-weight: bold; text-align: center;border: 1px solid #000000;">Kabupaten</th>
        <th style="font-weight: bold; text-align: center;border: 1px solid #000000;">Bulan</th>
        <th style="font-weight: bold; text-align: center;border: 1px solid #000000;">Tahun</th>
        <th style="font-weight: bold; text-align: center;border: 1px solid #000000;">Status</th>
        <th style="font-weight: bold; text-align: center;border: 1px solid #000000;">Terverifikasi</th>
        <th style="font-weight: bold; text-align: center;border: 1px solid #000000;">Tanggal Kirim</th>
        <th style="font-weight: bold; text-align: center;border: 1px solid #000000;">Batas Pelaporan</th>
        <th style="font-weight: bold; text-align: center;border: 1px solid #000000;">Batas Pembayaran</th>
    </tr>
    @php
        $no = 1;
        // Indonesian month names
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
    @endphp
    @foreach($data as $pelaporan)
    <tr>
        <td style="border: 1px solid #000000; text-align: center;">{{ $no++ }}</td>
        <td style="border: 1px solid #000000;">{{ $pelaporan->user->name ?? '-' }}</td>
        <td style="border: 1px solid #000000;">{{ $pelaporan->user->userDetail->kabupaten->nama ?? '-' }}</td>
        <td style="border: 1px solid #000000;">{{ $monthNames[$pelaporan->bulan] ?? '-' }}</td>
        <td style="border: 1px solid #000000; text-align: center;">{{ $pelaporan->tahun }}</td>
        <td style="border: 1px solid #000000; text-align: center;">
            @if(!$pelaporan->is_sent_to_admin && !$pelaporan->catatan_revisi)
                Draft
            @elseif($pelaporan->catatan_revisi && !$pelaporan->is_sent_to_admin)
                Revisi
            @elseif($pelaporan->is_verified && !$pelaporan->is_sptpd_approved)
                Terverifikasi - Pending SPTPD
            @elseif($pelaporan->is_paid)
                Lunas
            @elseif($pelaporan->is_sptpd_approved)
                Pending Pembayaran SSPD
            @else
                Verifikasi Admin
            @endif
        </td>
        <td style="border: 1px solid #000000; text-align: center;">{{ $pelaporan->is_verified ? 'Ya' : 'Tidak' }}</td>
        <td style="border: 1px solid #000000; text-align: center;">
            {{ $pelaporan->first_send_at ? $pelaporan->first_send_at->format('d/m/Y H:i') : '-' }}
        </td>
        <td style="border: 1px solid #000000; text-align: center;">
            {{ $pelaporan->batas_pelaporan ? $pelaporan->batas_pelaporan->format('d/m/Y') : '-' }}
        </td>
        <td style="border: 1px solid #000000; text-align: center;">
            {{ $pelaporan->batas_pembayaran ? $pelaporan->batas_pembayaran->format('d/m/Y') : '-' }}
        </td>
    </tr>
    @endforeach
    @if($data->isEmpty())
    <tr>
        <td colspan="10"
            style="border: 1px solid #000000; text-align: center;">Tidak ada data</td>
    </tr>
    @endif
</table>
