@extends('layouts.dashboard-base')

@push('styles')
    <style>
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #435ebe;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .section-title::before {
            content: "";
            width: 4px;
            height: 24px;
            background-color: #435ebe;
            display: inline-block;
            margin-right: 10px;
            border-radius: 4px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border: none;
        }

        .table-container {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            background-color: #eef1fa;
            color: #435ebe;
            font-weight: 600;
            border-top: none;
            padding: 12px 15px;
        }

        .table thead th:first-child {
            border-top-left-radius: 8px;
        }

        .table thead th:last-child {
            border-top-right-radius: 8px;
        }

        .table-secondary-custom {
            background-color: transparent;
        }

        .table tbody tr:hover {
            background-color: #f0f4f9;
        }

        .table-totals {
            background-color: #eef1fa !important;
            font-weight: 600;
            color: #435ebe;
        }

        .summary-box {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1.2rem 1.5rem;
            margin-bottom: 1rem;
            border-left: 4px solid #435ebe;
        }

        .summary-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: #555;
            margin-bottom: 0.3rem;
        }

        .summary-value {
            font-size: 1.2rem;
            font-weight: 700;
            color: #435ebe;
        }

        .payment-btn {
            padding: 0.8rem;
            font-weight: 600;
            border-radius: 8px;
            margin-top: 2rem;
            transition: all 0.3s ease;
        }

        .payment-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 94, 190, 0.3);
        }

        .section-icon {
            margin-right: 8px;
            font-size: 1.2rem;
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3 class="mb-0">SSPD</h3>
                    <p class="text-muted">Surat Setoran Pajak Daerah</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb"
                         class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('pelaporan.index') }}">Pelaporan</a>
                            </li>
                            <li aria-current="page"
                                class="breadcrumb-item active">SSPD - Data Penjualan {{ $pelaporan->bulan_name }}
                                {{ $pelaporan->tahun }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h4 class="section-title">
                        <i class="bi bi-fuel-pump section-icon"></i>
                        1. Data Objek Pajak
                    </h4>
                    <div class="table-container">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="40%">Nama BBKB</th>
                                    <th width="25%">Volume (Liter)</th>
                                    <th width="25%">Harga Jual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pelaporan->data_formatted as $nama_bbm => $item)
                                    <tr class="table-secondary-custom">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $nama_bbm }}</td>
                                        <td>{{ number_format($item->get('volume'), 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($item->get('dpp'), 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                <tr class="table-totals">
                                    <td colspan="2">Total</td>
                                    <td>{{ number_format($pelaporan->total_volume, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($pelaporan->total_dpp, 2, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h4 class="section-title">
                        <i class="bi bi-cash-stack section-icon"></i>
                        2. Jumlah Pajak Terhutang
                    </h4>
                    <div class="table-container">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="65%">Nama BBKB</th>
                                    <th width="25%">Pajak Terutang</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pelaporan->data_formatted as $nama_bbm => $item)
                                    <tr class="table-secondary-custom">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $nama_bbm }}</td>
                                        <td>Rp {{ number_format($item->get('pbbkb'), 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                <tr class="table-totals">
                                    <td colspan="2">Total</td>
                                    <td>Rp {{ number_format($pelaporan->total_pbbkb, 2, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h4 class="section-title">
                        <i class="bi bi-exclamation-triangle section-icon"></i>
                        3. Sanksi Administrasi
                    </h4>
                    <div class="table-container">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="25%">Waktu</th>
                                    <th width="40%">Keterangan</th>
                                    <th width="25%">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pelaporan->denda as $item)
                                    <tr class="table-secondary-custom">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->waktu_denda->format('d-m-Y') }}</td>
                                        <td>{{ $item->keterangan }}</td>
                                        <td>Rp {{ number_format($item->denda, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                @foreach ($pelaporan->bunga as $item)
                                    <tr class="table-secondary-custom">
                                        <td>{{ $loop->iteration + count($pelaporan->denda) }}</td>
                                        <td>{{ $item->waktu_bunga->format('d-m-Y') }}</td>
                                        <td>{{ $item->keterangan }}</td>
                                        <td>Rp {{ number_format($item->bunga, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                <tr class="table-totals">
                                    <td colspan="3">Total</td>
                                    <td>Rp
                                        {{ number_format($pelaporan->denda->sum('denda') + $pelaporan->bunga->sum('bunga'), 2, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h4 class="section-title">
                        <i class="bi bi-journal-check section-icon"></i>
                        4. Rekapitulasi
                    </h4>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="summary-box">
                                <div class="summary-title">Total Pajak Terutang</div>
                                <div class="summary-value">Rp {{ number_format($pelaporan->total_pbbkb, 2, ',', '.') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="summary-box">
                                <div class="summary-title">Total Sanksi Administrasi</div>
                                <div class="summary-value">Rp
                                    {{ number_format($pelaporan->denda->sum('denda') + $pelaporan->bunga->sum('bunga'), 2, ',', '.') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="summary-box"
                                 style="border-left: 4px solid #198754;">
                                <div class="summary-title">Total Keseluruhan</div>
                                <div class="summary-value"
                                     style="color: #198754;">
                                    Rp
                                    {{ number_format($pelaporan->total_pbbkb + $pelaporan->denda->sum('denda') + $pelaporan->bunga->sum('bunga'), 2, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="{{route('invoices.show', $pelaporan->ulid)}}" class="btn btn-primary payment-btn d-block w-100">
                        <i class="bi bi-credit-card me-2"></i>
                        Lanjutkan ke Pembayaran
                    </a>
                </div>
            </div>
        </section>
    </div>
@endsection
