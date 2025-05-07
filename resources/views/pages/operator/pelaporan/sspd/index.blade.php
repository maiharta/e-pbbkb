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
                    <p class="mt-3 mb-2 fw-bolder">2. Jumlah Pajak Terhutang Berdasarkan Angka Sementara Untuk Masa Pajak Sekarang</p>
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
                    <button class="btn btn-primary d-block w-100"><span class="isax isax-add-square"></span> Pembayaran</button>
                </div>
            </div>
        </section>
    </div>
@endsection
