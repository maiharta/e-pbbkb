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

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h2,
        .header h3 {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th,
        td {
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .section {
            margin-bottom: 15px;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .signature-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .signature-box {
            float: right;
            width: 45%;
            text-align: center;
        }

        .signature-line {
            margin-top: 70px;
            border-top: 1px solid #000;
            width: 80%;
            display: inline-block;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .bold {
            font-weight: bold;
        }

        .total-row {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(200, 200, 200, 0.2);
            z-index: -1;
        }
    </style>
</head>

<body>
    @if ($pelaporan->is_paid)
        <div class="watermark">LUNAS</div>
    @endif

    <div class="header">
        <h2>KOP PERANGKAT DAERAH/UPTD PEMUNGUT</h2>
        <h3>SURAT SETORAN PAJAK DAERAH (SSPD)</h3>
    </div>

    <div class="section">
        <table>
            <tr>
                <td width="25%"><strong>Nama Wajib Pajak</strong></td>
                <td width="75%">: {{ $pelaporan->user->name }}</td>
            </tr>
            <tr>
                <td><strong>NPWPD</strong></td>
                <td>: {{ $pelaporan->user->userDetail->npwpd ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Alamat</strong></td>
                <td>: {{ $pelaporan->user->userDetail->alamat ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Masa Pajak</strong></td>
                <td>: {{ $pelaporan->bulan_name }} {{ $pelaporan->tahun }}</td>
            </tr>
            <tr>
                <td><strong>Nomor SPTPD</strong></td>
                <td>: {{ $pelaporan->sptpd->nomor }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal SPTPD</strong></td>
                <td>: {{ $pelaporan->sptpd->tanggal->format('d-m-Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">1. Data Objek Pajak</div>
        <table>
            <thead>
                <tr>
                    <th width="5%">No.</th>
                    <th width="35%">Nama BBKB</th>
                    <th width="20%">Volume (Liter)</th>
                    <th width="20%">Dasar Pengenaan Pajak</th>
                    <th width="20%">Pajak Terutang</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pelaporan->data_formatted as $nama_bbm => $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $nama_bbm }}</td>
                        <td class="text-right">{{ number_format($item->get('volume'), 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->get('dpp'), 2, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->get('pbbkb'), 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td class="text-center"
                        colspan="2"><strong>TOTAL</strong></td>
                    <td class="text-right">{{ number_format($pelaporan->total_volume, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($pelaporan->total_dpp, 2, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($pelaporan->total_pbbkb, 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">2. Sanksi Administrasi</div>
        <table>
            <thead>
                <tr>
                    <th width="5%">No.</th>
                    <th width="15%">Tanggal</th>
                    <th width="60%">Keterangan</th>
                    <th width="20%">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pelaporan->denda as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $item->waktu_denda->format('d-m-Y') }}</td>
                        <td>{{ $item->keterangan }}</td>
                        <td class="text-right">Rp {{ number_format($item->denda, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                @foreach ($pelaporan->bunga as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration + count($pelaporan->denda) }}</td>
                        <td>{{ $item->waktu_bunga->format('d-m-Y') }}</td>
                        <td>{{ $item->keterangan }}</td>
                        <td class="text-right">Rp
                            {{ number_format($item->bunga * $pelaporan->sptpd->total_pbbkb, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td class="text-center"
                        colspan="3"><strong>TOTAL SANKSI</strong></td>
                    <td class="text-right">Rp {{ number_format($total_sanksi, 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">3. Rekapitulasi</div>
        <table>
            <tr>
                <td width="80%"><strong>Jumlah Pajak Terutang</strong></td>
                <td class="text-right"
                    width="20%">Rp {{ number_format($pelaporan->total_pbbkb, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Jumlah Sanksi Administrasi</strong></td>
                <td class="text-right">Rp {{ number_format($total_sanksi, 2, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td><strong>JUMLAH YANG HARUS DIBAYAR</strong></td>
                <td class="text-right">Rp {{ number_format($grand_total, 2, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="signature-section clearfix">
        <div class="signature-box">
            <p>{{ $pelaporan->is_paid ? 'Telah dibayar' : 'Akan dibayar' }} tanggal:
                {{ $pelaporan->is_paid ? $pelaporan->updated_at->format('d-m-Y') : '........................' }}</p>
            <p>Wajib Pajak</p>
            <div class="signature-line"></div>
            <p>{{ $pelaporan->user->name }}</p>
        </div>
    </div>

    <div style="margin-top: 20px; font-size: 10px; text-align: center;">
        <p>Surat Setoran Pajak Daerah ini dicetak secara elektronik melalui Aplikasi E-PBBKB Provinsi Bali</p>
    </div>
</body>

</html>
