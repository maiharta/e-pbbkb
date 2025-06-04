{{-- BUKTI PEMBAYARAN PAJAK BAHAN BAKAR KENDARAAN BERMOTOR --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0"
          name="viewport">
    <title>Bukti Pembayaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        h4 {
            margin-bottom: 20px;
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
        table{
            font-size: 12px;
        }
    </style>
</head>

<body>
    <h4 style="text-align: center;">BUKTI PEMBAYARAN PAJAK BAHAN BAKAR KENDARAAN BERMOTOR</h4>
    <br>
    <table style=""
           width="100%">
        <tr>
            <td>Kode Bukti Pembayaran PBBKB : {{ $invoice->invoice_number }}</td>
        </tr>
        <tr>
            <td>Wajib Pungut Kena Pajak</td>
        </tr>
    </table>
    <table class="no-border-y"
           width="100%">
        <tr>
            <td>Nama : {{ $invoice->customer_name }}</td>
        </tr>
        <tr>
            <td>Alamat : {{ $invoice->customer_address }}</td>
        </tr>
        <tr>
            <td>NPWP : {{ $invoice->customer_npwpd }}</td>
        </tr>
    </table>
    <table style=""
           width="100%">
        <tr>
            <td>Pembeli Kena Pajak / Penerima Jasa Kena Pajak</td>
        </tr>
    </table>
    <table class="no-border-y"
           width="100%">
        <tr>
            <td>Nama : {{ $pelaporan->penjualan->first()->pembeli }}</td>
        </tr>
        <tr>
            <td>Alamat : {{ $pelaporan->penjualan->first()->alamat }}</td>
        </tr>
        <tr>
            <td>NPWP : {{ $pelaporan->penjualan->first()->npwp }}</td>
        </tr>
    </table>
    <table style=""
           width="100%">
        <tr>
            <td>No</td>
            <td>Jenis Bahan Bakar</td>
            <td>Quantity (Ltr)</td>
            <td>DPP</td>
            <td>Tarif BBM</td>
            <td>Tarif Sektor</td>
            <td>PBBKB</td>
        </tr>
        @foreach ($pelaporan->penjualan_group as $penjualan)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $penjualan->first()->nama_jenis_bbm }}</td>
                <td>{{ number_format($penjualan->sum('volume'),0, ',', '.') }}</td>
                <td>{{ 'Rp. ' . number_format($penjualan->sum('dpp'),0, ',', '.') }}</td>
                <td>{{ (int) $penjualan->first()->persentase_tarif_jenis_bbm }}%</td>
                <td>{{ (int) $penjualan->first()->persentase_pengenaan_sektor }}%</td>
                <td>{{ 'Rp. ' . number_format($penjualan->sum('pbbkb_sistem'),0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>
    <p style="font-size: 12px">Bukti pembayaran Pajak Bahan Bakar Kendaraan Bermotor dikeluarkan oleh Badan Pendapatan Daerah Provinsi Bali dan dicetak secara elektronik sesuai dengan data yang telah dilaporkan oleh wajib pungut melalui aplikasi E-PBBKB.</p>
</body>
