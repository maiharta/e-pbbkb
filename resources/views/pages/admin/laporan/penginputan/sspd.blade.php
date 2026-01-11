@extends('layouts.dashboard-base')

@push('styles')
    <style>
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #435ebe;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border: none;
        }

        .table thead th {
            background-color: #eef1fa;
            color: #435ebe;
            font-weight: 600;
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
        }

        .summary-value {
            font-size: 1.2rem;
            font-weight: 700;
            color: #435ebe;
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6">
                    <h3>SSPD</h3>
                    <p class="text-muted">Surat Setoran Pajak Daerah</p>
                </div>
                <div class="col-12 col-md-6">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('laporan.penginputan.index') }}">Laporan Penginputan</a></li>
                            <li class="breadcrumb-item active">SSPD - {{ $pelaporan->bulan_name }} {{ $pelaporan->tahun }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h4 class="section-title"><i class="bi bi-fuel-pump me-2"></i>1. Data Objek Pajak</h4>
                    <div class="table-responsive">
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
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $nama_bbm }}</td>
                                        <td>{{ number_format($item->get('volume'), 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($item->get('dpp'), 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                <tr class="table-totals">
                                    <td colspan="2">JUMLAH</td>
                                    <td>{{ number_format($pelaporan->total_volume, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($pelaporan->total_dpp, 2, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h4 class="section-title"><i class="bi bi-calculator me-2"></i>2. Perhitungan PBBKB</h4>
                    <div class="summary-box">
                        <div class="summary-title">Total PBBKB yang Harus Disetor</div>
                        <div class="summary-value">Rp {{ number_format($pelaporan->total_pbbkb, 2, ',', '.') }}</div>
                    </div>

                    @if($pelaporan->bunga && $pelaporan->bunga->count() > 0)
                    <div class="summary-box">
                        <div class="summary-title">Total Bunga</div>
                        <div class="summary-value">Rp {{ number_format($pelaporan->bunga->sum('total_bunga'), 2, ',', '.') }}</div>
                    </div>
                    @endif

                    @if($pelaporan->denda && $pelaporan->denda->count() > 0)
                    <div class="summary-box">
                        <div class="summary-title">Total Denda</div>
                        <div class="summary-value">Rp {{ number_format($pelaporan->denda->sum('denda'), 2, ',', '.') }}</div>
                    </div>
                    @endif

                    <div class="summary-box" style="border-left-color: #198754; border-left-width: 6px;">
                        <div class="summary-title">TOTAL KESELURUHAN</div>
                        <div class="summary-value" style="color: #198754; font-size: 1.5rem;">
                            Rp {{ number_format(
                                $pelaporan->total_pbbkb +
                                ($pelaporan->bunga ? $pelaporan->bunga->sum('total_bunga') : 0) +
                                ($pelaporan->denda ? $pelaporan->denda->sum('denda') : 0),
                                2, ',', '.'
                            ) }}
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        <a class="btn btn-primary" href="{{ route('laporan.penginputan.sspd.download', $pelaporan->ulid) }}" target="_blank">
                            <i class="isax isax-document"></i> Unduh Dokumen SSPD
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
