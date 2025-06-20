<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0"
          name="viewport">
    <title>SSPD</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }

        @page {
            margin: 0cm 2.5cm;
        }

        .header {
            text-align: center;
        }

        .header h2 {
            margin: 0;
            padding: 0;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 4px;
            border: 1px solid black;
        }

        .no-border-y td {
            border-top: none;
            border-bottom: none;
        }

        table {
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="header">
        <img alt="Kop Surat"
             src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/images/kop_surat.png'))) }}"
             style="width: 100%; max-height: 150px;">
        <h2 style="margin-top:10px;">SURAT SETORAN PAJAK DAERAH (SSPD)</h2>
        <h2>PAJAK BAHAN BAKAR KENDARAAN BERMOTOR</h2>
        <h2>NOMOR : {{ $pelaporan->sspd_number }}</h2>
        <table style="margin-top: 20px;"
               width="100%">
            <tr>
                <td>Nama : {{ $pelaporan->invoices->first()->customer_name }}</td>
            </tr>
            <tr>
                <td style="border-bottom: none;">Alamat : {{ $pelaporan->invoices->first()->customer_address }}</td>
            </tr>
        </table>
        <table>
            <tr>
                <td>NOPD/NPWPD : {{ $pelaporan->invoices->first()->customer_npwpd }}</td>
                <td>Masa : {{ $pelaporan->bulan_name }} {{ $pelaporan->tahun }}</td>
            </tr>
            <tr>
                <td>Dasar Setoran : SPTPD</td>
                <td>Tgl. Bayar : {{ $pelaporan->invoices->first()->sipay_payment_date_paid->addDay()->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td>Nomor : {{ $pelaporan->sptpd_number }}</td>
                <td>UPTD : </td>
            </tr>
        </table>
        <table style="margin-top: 20px;"
               width="100%">
            <tr>
                <td style="font-weight: bold; text-align: center;">No</td>
                <td style="font-weight: bold; text-align: center;">Jenis Penerimaan</td>
                <td style="font-weight: bold; text-align: center;">Kuantitas</td>
                <td style="font-weight: bold; text-align: center;">Jumlah (Rp)</td>
            </tr>
            @foreach ($pelaporan->penjualan as $penjualan)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $penjualan->get('nama_jenis_bbm') }}</td>
                    <td>{{ number_format($penjualan->get('volume'), 0, ',', '.') }} Liter</td>
                    <td style="text-align: right;">{{ number_format($penjualan->get('pbbkb'), 0, ',', '.') }}</td>
                </tr>
            @endforeach
            {{-- total --}}
            <tr>
                <td colspan="3" style="text-align: right;">Jumlah</td>
                <td style="text-align: right;">{{ number_format($pelaporan->penjualan->sum('pbbkb'), 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;">Sanksi Administratif</td>
                <td style="text-align: right;">{{ number_format($pelaporan->denda->sum('denda') + $pelaporan->bunga->sum('bunga') * $pelaporan->sptpd->total_pbbkb, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;">Total</td>
                <td style="text-align: right;">{{ number_format($pelaporan->penjualan->sum('pbbkb') + $pelaporan->denda->sum('denda') + $pelaporan->bunga->sum('bunga') * $pelaporan->sptpd->total_pbbkb, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>
</body>

</html>
