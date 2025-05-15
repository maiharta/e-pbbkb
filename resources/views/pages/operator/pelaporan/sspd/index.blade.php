@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>SSPD</h3>
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
                    <p class="mt-3 mb-2 fw-bolder">1. Data Objek Pajak</p>
                    <div class="table-responsive ms-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama BBKB</th>
                                    <th>Volume (Liter)</th>
                                    <th>Harga Jual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pelaporan->data_formatted as $nama_bbm => $item)
                                    <tr class="table-secondary-custom">
                                        <td width="10%">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td width="40%">
                                            {{ $nama_bbm }}
                                        </td>
                                        <td width="25%">
                                            {{ number_format($item->get('volume'), 0, ',', '.') }}
                                        </td>
                                        <td width="25%">
                                            Rp {{ number_format($item->get('dpp'), 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="table-secondary-custom">
                                    <td class="fw-bold"
                                        colspan="2">Total</td>
                                    <td class="fw-bold">{{ number_format($pelaporan->total_volume, 0, ',', '.') }}</td>
                                    <td class="fw-bold">Rp {{ number_format($pelaporan->total_dpp, 2, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-3 mb-2 fw-bolder">2. Jumlah Pajak Terhutang Berdasarkan Angka Sementara Untuk Masa Pajak
                        Sekarang</p>
                    <div class="table-responsive ms-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama BBKB</th>
                                    <th>Pajak Terutang</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pelaporan->data_formatted as $nama_bbm => $item)
                                    <tr class="table-secondary-custom">
                                        <td width="10%">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td width="40%">
                                            {{ $nama_bbm }}
                                        </td>
                                        <td width="25%">
                                            Rp {{ number_format($item->get('pbbkb'), 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="table-secondary-custom">
                                    <td class="fw-bold"
                                        colspan="2">Total</td>
                                    <td class="fw-bold">Rp {{ number_format($pelaporan->total_pbbkb, 2, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-3 mb-2 fw-bolder">3 Sanksi Administrasi</p>
                    <div class="table-responsive ms-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Waktu</th>
                                    <th>Keterangan</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pelaporan->denda as $item)
                                    <tr class="table-secondary-custom">
                                        <td width="10%">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td width="25%">
                                            {{ $item->waktu_denda->format('d-m-Y') }}
                                        </td>
                                        <td width="40%">
                                            {{ $item->keterangan }}
                                        </td>
                                        <td width="25%">
                                            Rp {{ number_format($item->denda, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                                @foreach ($pelaporan->bunga as $item)
                                    <tr class="table-secondary-custom">
                                        <td width="10%">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td width="25%">
                                            {{ $item->waktu_bunga->format('d-m-Y') }}
                                        </td>
                                        <td width="40%">
                                            {{ $item->keterangan }}
                                        </td>
                                        <td width="25%">
                                            Rp {{ number_format($item->bunga, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="table-secondary-custom">
                                    <td class="fw-bold"
                                        colspan="3">Total</td>
                                    <td class="fw-bold">Rp
                                        {{ number_format($pelaporan->denda->sum('denda') + $pelaporan->bunga->sum('bunga'), 2, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="mt-3 mb-2 fw-bolder">4 Total Jumlah Pajak Terutang dengan Sanksi Administrasi</p>
                    <p class="mt-3 mb-2 fw-bolder">Total: Rp {{ number_format($pelaporan->total_pbbkb, 2, ',', '.') }}
                    </p>
                    <p class="mt-3 mb-2 fw-bolder">Total Sanksi Administrasi: Rp
                        {{ number_format($pelaporan->denda->sum('denda') + $pelaporan->bunga->sum('bunga'), 2, ',', '.') }}
                    </p>
                    <p class="mt-3 mb-2 fw-bolder">Total Jumlah Pajak Terutang dengan Sanksi Administrasi: Rp
                        {{ number_format($pelaporan->total_pbbkb + $pelaporan->denda->sum('denda') + $pelaporan->bunga->sum('bunga'), 2, ',', '.') }}
                    </p>
                    <button class="btn btn-primary d-block w-100"><span class="isax isax-add-square"></span>
                        Pembayaran</button>
                </div>
            </div>
        </section>
    </div>
@endsection
