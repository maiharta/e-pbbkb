<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0"
          name="viewport">
    <title>SPTPD</title>
    <style>
        body {
            border: 1px solid black;
            padding: 0;
            margin: 0;
        }

        section.one tr td:first-child,
        section.two tr td {
            width: 60%;
        }

        section.one tr td {
            font-weight: bold;
        }

        /* To ensure other columns take the remaining space */
        section.one tr td:not(:first-child) width: 40%;
        }

        section.two p {
            text-align: center;
            font-weight: bold;
        }

        section.three table td {
            border: 1px solid black;
        }
    </style>
</head>

<body>
    <section class="one">
        <table width="100%">
            <tr>
                <td>PEMERINTAH PROVINSI BALI</td>
                <td>No. SPTPD : {{ $pelaporan->sptpd_number }}</td>
            </tr>
            <tr>
                <td>BADAN PENDAPATAN DAERAH</td>
                <td>Masa Pajak : {{ $pelaporan->bulan_name }}</td>
            </tr>
            <tr>
                <td>Jl. Kapten Cok Agung Tresna Nomor 14.</td>
                <td>Tahun Pajak : {{ $pelaporan->tahun }}</td>
            </tr>
        </table>
    </section>
    <hr>
    <section class="two">
        <p>SPTPD</p>
        <p>(SURAT PEMBERITAHUAN PAJAK DAERAH)</p>
        <p>PAJAK BAHAN BAKAR KENDARAAN BERMOTOR</p>
        <br>
        <div style="width:100%">
            <div style="float:left; width: 50%;">
                <table width="100%">
                    <tr>
                        <td>NAMA : {{ $pelaporan->user->name }}</td>
                    </tr>
                    <tr>
                        <td>ALAMAT : {{ $pelaporan->user->userDetail->alamat }}</td>
                    </tr>
                    <tr>
                        <td>NPWPD : {{ $pelaporan->user->userDetail->npwpd }}</td>
                    </tr>
                </table>
            </div>
            <div style="float:right; width: 50%;">
                <table width="100%">
                    <tr>
                        <td>Yth:</td>
                    </tr>
                    <tr>
                        <td>KELAPA BADAN PENDAPATAN DAERAH</td>
                    </tr>
                    <tr>
                        <td>PROVINSI BALI</td>
                    </tr>
                    <tr>
                        <td>DI DENPASAR</td>
                    </tr>
                </table>
            </div>
            <div style="clear: both;"></div>
        </div>
    </section>
    <hr>
    <section class="three">
        <p style="margin: 0; padding: 0;">PERHATIAN</p>
        <ol>
            <li>Harap diisi dalam rangap 2 (dua)</li>
            <li>Setelah diisi dan ditandatangani, harap diserahkan kembali kepada Badan Pendapatan Daerah Provinsi Bali,
                paling lambat 10 (sepuluh) hari kerja bulan berikutnya (Self Assesment)</li>
            <li>Apabila pembayaran dilakukan setelah jatuh tempo, dikenakan sanki administratif berupa bunga 1% per
                bulan
            </li>
        </ol>
        <hr>
        <h4 style="text-align: center;margin:0; padding:0;">DIISI OLEH {{ $pelaporan->user->name }}</h4>
        <hr>
        1. Data Objek Pajak
        <table style="border-collapse: collapse; border: 1px solid black;"
               width="100%">
            <tr>
                <th>No</th>
                <th>Nama BBKB</th>
                <th>Volume (Ltr)</th>
                <th>Bulan</th>
                <th>Harga Jual(Rp)</th>
            </tr>
            @foreach ($pelaporan->penjualan_by_jenis as $penjualan)
                <tr>
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td style="">{{ $penjualan->get('nama_jenis_bbm') }}</td>
                    <td style="text-align: center;">{{ number_format($penjualan->get('volume'), 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ $pelaporan->bulan_name }}</td>
                    <td style="text-align: center;">{{ number_format($penjualan->get('dpp'), 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </table>
        <br>
        <hr>
        <h4 style="text-align: center;margin:0; padding:0;">DIISI OLEH WP <i>SELF ASSESMENT</i></h4>
        <hr>
        1. Jumlah Pajak Terhutang untuk Masa Pajak Sebelumnya (akumulasi dari awal Masa Pajak dalam Tahun Pajak
        Tertentu)
        <table style="border-collapse: collapse; border: 1px solid black;"
               width="100%">
            <tr>
                <th>No.</th>
                <th>Masa Pajak</th>
                <th>Nama BBKB</th>
                <th>Harga Jual (Rp)</th>
                <th>Tarif PBBKB (Sesuai Perda)</th>
                <th>Pajak Terutang (Rp)</th>
            </tr>
            <tr>
                <td colspan="5">JUMLAH(1)</td>
                <td></td>
            </tr>
        </table>
        2. Jumlah Pajak Terhutang berdasarkan angka sementara untuk Masa Pajak sekarang (Lampirkan fotocopy dokumen)
        <table style="border-collapse: collapse; border: 1px solid black;"
               width="100%">
            <tr>
                <th>No.</th>
                <th>Masa Pajak</th>
                <th>Nama BBKB</th>
                <th>Harga Jual (Rp)</th>
                <th>Tarif PBBKB (Sesuai Perda)</th>
                <th>Pajak Terutang (Rp)</th>
            </tr>
            @php $counter = 1; @endphp
            @foreach ($pelaporan->penjualan_by_sektor as $penjualan_by_sektor)
                @foreach ($penjualan_by_sektor as $penjualan)
                    <tr>
                        <td style="text-align: center;">{{ $counter }}</td>
                        <td style="text-align: center;">{{ $pelaporan->bulan_name }}</td>
                        <td style="">{{ $penjualan->get('nama_jenis_bbm') }} -
                            {{ $penjualan->get('nama_sektor') }}</td>
                        <td style="text-align: center;">{{ number_format($penjualan->get('dpp'), 0, ',', '.') }}</td>
                        <td style="text-align: center;">
                            {{ number_format($penjualan->get('persentase_pengenaan_sektor'), 0, ',', '.') }} %</td>
                        <td style="text-align: center;">{{ number_format($penjualan->get('pbbkb'), 0, ',', '.') }}</td>
                    </tr>
                    @php $counter++; @endphp
                @endforeach
            @endforeach
            <tr>
                <td colspan="5">JUMLAH(2)</td>
                <td style="text-align: center;">
                    {{ number_format($pelaporan->penjualan->sum('pbbkb'), 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td colspan="6"> </td>
            </tr>
            <tr>
                <td style="text-align: center;" colspan="5">TOTAL (1+2)</td>
                <td style="text-align: center;">
                    {{ number_format($pelaporan->penjualan->sum('pbbkb'), 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td colspan="6"> </td>
            </tr>
        </table>
    </section>
</body>
