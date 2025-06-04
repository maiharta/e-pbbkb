<!-- filepath: c:\Users\Admin\Documents\project\maiharta\e-pbbkb\resources\views\pdf\payment-proof.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bukti Pembayaran</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12pt;
            line-height: 1.4;
            color: #333;
        }
        .container {
            width: 100%;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #435ebe;
            padding-bottom: 10px;
        }
        .header img {
            max-height: 80px;
        }
        .header h1 {
            color: #435ebe;
            margin: 5px 0;
            font-size: 24pt;
        }
        .payment-details {
            margin-bottom: 30px;
        }
        .payment-details table {
            width: 100%;
        }
        .payment-details td {
            padding: 5px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background-color: #435ebe;
            color: white;
            padding: 8px;
            text-align: left;
        }
        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .total-row {
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10pt;
            color: #666;
        }
        .watermark {
            position: absolute;
            top: 45%;
            left: 25%;
            transform: rotate(-45deg);
            opacity: 0.15;
            font-size: 70pt;
            color: #435ebe;
            font-weight: bold;
            z-index: -1;
        }
        .paid-stamp {
            position: absolute;
            top: 200px;
            right: 50px;
            transform: rotate(15deg);
            border: 5px solid #28a745;
            color: #28a745;
            font-weight: bold;
            font-size: 24pt;
            padding: 10px 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="watermark">LUNAS</div>
    <div class="paid-stamp">LUNAS</div>

    <div class="container">
        <div class="header">
            <img src="{{ public_path('images/logo.png') }}" alt="Logo">
            <h1>BUKTI PEMBAYARAN</h1>
            <p>Badan Pendapatan Daerah Provinsi Bali</p>
        </div>

        <div class="payment-details">
            <table>
                <tr>
                    <td width="180"><strong>No. Invoice</strong></td>
                    <td>: {{ $invoice->invoice_number }}</td>
                </tr>
                <tr>
                    <td><strong>Tanggal Pembayaran</strong></td>
                    <td>: {{ $paymentDate }}</td>
                </tr>
                <tr>
                    <td><strong>NPWPD</strong></td>
                    <td>: {{ $invoice->customer_npwpd }}</td>
                </tr>
                <tr>
                    <td><strong>Nama</strong></td>
                    <td>: {{ $invoice->customer_name }}</td>
                </tr>
                <tr>
                    <td><strong>Alamat</strong></td>
                    <td>: {{ $invoice->customer_address }}</td>
                </tr>
                <tr>
                    <td><strong>Periode</strong></td>
                    <td>: {{ $invoice->description }}</td>
                </tr>
            </table>
        </div>

        <h3>Rincian Pembayaran</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Deskripsi</th>
                    <th>Nomor Kwitansi</th>
                    <th style="text-align: right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($invoice->items['pbbkb']))
                <tr>
                    <td>{{ $invoice->items['pbbkb']['keterangan'] }}</td>
                    <td>{{ $invoice->items['pbbkb']['nomor_kwitansi'] }}</td>
                    <td style="text-align: right">Rp {{ number_format($invoice->items['pbbkb']['nominal'], 2, ',', '.') }}</td>
                </tr>
                @endif

                @if(isset($invoice->items['sanksi']))
                <tr>
                    <td>{{ $invoice->items['sanksi']['keterangan'] }}</td>
                    <td>{{ $invoice->items['sanksi']['nomor_kwitansi'] }}</td>
                    <td style="text-align: right">Rp {{ number_format($invoice->items['sanksi']['nominal'], 2, ',', '.') }}</td>
                </tr>
                @endif

                <tr class="total-row">
                    <td colspan="2" style="text-align: right"><strong>Total</strong></td>
                    <td style="text-align: right"><strong>Rp {{ number_format($invoice->amount, 2, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            <p>Dokumen ini dihasilkan secara otomatis dan sah tanpa tanda tangan.</p>
            <p>Dicetak pada: {{ $generateDate }}</p>
        </div>
    </div>
</body>
</html>
