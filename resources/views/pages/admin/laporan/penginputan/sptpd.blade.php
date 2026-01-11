@extends('layouts.dashboard-base')

@push('styles')
    <style>
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #435ebe;
            margin-top: 1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .section-title::before {
            content: "";
            width: 4px;
            height: 20px;
            background-color: #435ebe;
            display: inline-block;
            margin-right: 10px;
            border-radius: 4px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: none;
            margin-bottom: 1.5rem;
        }

        .card-header {
            background-color: rgba(67, 94, 190, 0.05);
            border-bottom: 1px solid rgba(67, 94, 190, 0.1);
            padding: 1rem 1.5rem;
        }

        .info-table {
            width: 100%;
        }

        .info-table td {
            padding: 0.5rem 0;
            vertical-align: top;
        }

        .info-table td:first-child {
            color: #6c757d;
            width: 180px;
            font-weight: 500;
        }

        .info-table td:last-child {
            font-weight: 600;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            background-color: rgba(67, 94, 190, 0.05);
            color: #435ebe;
            font-weight: 600;
            border-top: none;
            padding: 12px 15px;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: rgba(67, 94, 190, 0.02);
        }

        .sector-title {
            background-color: #f8f9fa;
            padding: 0.75rem 1rem;
            border-radius: 6px;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
            color: #435ebe;
            border-left: 3px solid #435ebe;
        }

        .category-title {
            margin-top: 1rem;
            margin-bottom: 0.75rem;
            font-weight: 500;
            color: #495057;
            padding-left: 1rem;
            border-left: 2px solid #6c757d;
        }

        .subtotal-row td {
            background-color: rgba(67, 94, 190, 0.05);
            font-weight: 600;
        }

        .total-row td {
            background-color: rgba(67, 94, 190, 0.1);
            font-weight: 700;
            color: #435ebe;
        }

        .btn-action {
            border-radius: 6px;
            font-weight: 500;
            padding: 0.6rem 1.5rem;
            transition: all 0.2s ease-in-out;
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>SPTPD</h3>
                    <p class="text-muted">Surat Pemberitahuan Pajak Daerah</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb"
                         class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('laporan.penginputan.index') }}">Laporan Penginputan</a></li>
                            <li aria-current="page"
                                class="breadcrumb-item active">SPTPD - {{ $pelaporan->bulan_name }}
                                {{ $pelaporan->tahun }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <!-- Company Information Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-building me-2"></i> Informasi Perusahaan</h5>
                </div>
                <div class="card-body">
                    <table class="info-table">
                        <tr>
                            <td>Nama Perusahaan</td>
                            <td>: {{ $pelaporan->user->name }}</td>
                        </tr>
                        <tr>
                            <td>NPWPD</td>
                            <td>: {{ $pelaporan->user->userDetail->npwpd }}</td>
                        </tr>
                        <tr>
                            <td>Periode Laporan</td>
                            <td>: {{ $pelaporan->bulan_name }} {{ $pelaporan->tahun }}</td>
                        </tr>
                        @if($pelaporan->sptpd_approved_at)
                        <tr>
                            <td>Tanggal Approval SPTPD</td>
                            <td>: {{ Carbon\Carbon::parse($pelaporan->sptpd_approved_at)->locale('id')->isoFormat('LL') }}</td>
                        </tr>
                        @endif
                        @if($pelaporan->sptpd_number)
                        <tr>
                            <td>Nomor SPTPD</td>
                            <td>: {{ $pelaporan->sptpd_number }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Sales Data Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i> Data Penjualan</h5>
                </div>
                <div class="card-body">
                    @foreach ($pelaporan->data_formatted as $sektor => $categories)
                        <div class="sector-title">
                            <i class="bi bi-chevron-right me-2"></i>{{ $loop->iteration }}. {{ $sektor }}
                        </div>

                        <div class="ms-3">
                            @foreach ($categories as $category => $items)
                                <div class="category-title">
                                    {{ chr(64 + $loop->iteration) }}. {{ $category }}
                                </div>

                                <div class="table-responsive mb-4">
                                    <table class="table table-hover table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="5%">No</th>
                                                <th width="15%">BBM</th>
                                                <th width="10%">Tarif</th>
                                                <th width="20%">Volume (Liter)</th>
                                                <th width="25%">DPP</th>
                                                <th width="25%">PBBKB</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($items['items'] as $key => $item)
                                                <tr>
                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                    <td>{{ $item->get('nama_jenis_bbm') }}</td>
                                                    <td class="text-center">{{ $item->get('persentase_tarif') }}%</td>
                                                    <td class="text-end">
                                                        {{ number_format($item->get('volume'), 0, ',', '.') }}</td>
                                                    <td class="text-end">Rp
                                                        {{ number_format($item->get('dpp'), 2, ',', '.') }}</td>
                                                    <td class="text-end">Rp
                                                        {{ number_format($item->get('pbbkb'), 2, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="subtotal-row">
                                                <td class="text-end"
                                                    colspan="3">SUBTOTAL</td>
                                                <td class="text-end">
                                                    {{ number_format($items['subtotal']->get('volume'), 0, ',', '.') }}
                                                </td>
                                                <td class="text-end">Rp
                                                    {{ number_format($items['subtotal']->get('dpp'), 2, ',', '.') }}</td>
                                                <td class="text-end">Rp
                                                    {{ number_format($items['subtotal']->get('pbbkb'), 2, ',', '.') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                        </div>

                        @if ($loop->last)
                            <div class="sector-title mt-4">
                                <i class="bi bi-chevron-right me-2"></i>{{ $loop->iteration + 1 }}. Total Keseluruhan
                            </div>

                            <div class="table-responsive ms-3">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="25%">Sektor</th>
                                            <th width="20%">Volume (Liter)</th>
                                            <th width="25%">DPP</th>
                                            <th width="25%">PBBKB</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pelaporan->data_formatted as $sektor => $categories)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td>{{ $sektor }}</td>
                                                <td class="text-end">
                                                    {{ number_format($categories->values()->pluck('subtotal')->sum('volume'), 0, ',', '.') }}
                                                </td>
                                                <td class="text-end">Rp
                                                    {{ number_format($categories->values()->pluck('subtotal')->sum('dpp'), 2, ',', '.') }}
                                                </td>
                                                <td class="text-end">Rp
                                                    {{ number_format($categories->values()->pluck('subtotal')->sum('pbbkb'), 2, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="total-row">
                                            <td class="text-end"
                                                colspan="2">TOTAL</td>
                                            <td class="text-end">
                                                {{ number_format($pelaporan->data_formatted->values()->map(fn($item) => $item->values()->pluck('subtotal')->sum('volume'))->sum(), 0, ',', '.') }}
                                            </td>
                                            <td class="text-end">
                                                Rp
                                                {{ number_format($pelaporan->data_formatted->values()->map(fn($item) => $item->values()->pluck('subtotal')->sum('dpp'))->sum(), 2, ',', '.') }}
                                            </td>
                                            <td class="text-end">
                                                Rp
                                                {{ number_format($pelaporan->data_formatted->values()->map(fn($item) => $item->values()->pluck('subtotal')->sum('pbbkb'))->sum(), 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endforeach

                    @if ($pelaporan->is_sptpd_approved)
                        <div class="d-flex justify-content-center mt-4">
                            <a class="btn btn-primary btn-action"
                               href="{{ route('laporan.penginputan.sptpd.download', $pelaporan->ulid) }}"
                               target="_blank">
                                <i class="isax isax-document"></i>
                                Unduh Dokumen SPTPD
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection
