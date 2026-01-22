@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6">
                    <h3>Data Penjualan</h3>
                    <p class="text-muted">{{ $pelaporan->user->name }} - {{ $pelaporan->bulan_name }} {{ $pelaporan->tahun }}</p>
                </div>
                <div class="col-12 col-md-6">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('laporan.penginputan.index') }}">Laporan Penginputan</a></li>
                            <li class="breadcrumb-item active">Data Penjualan</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="penjualan-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Invoice</th>
                                    <th>Nama Pembeli</th>
                                    <th>Sektor</th>
                                    <th>Jenis BBM</th>
                                    <th>Volume (Liter)</th>
                                    <th>DPP</th>
                                    <th>PBBKB</th>
                                    <th>Tanggal</th>
                                    <th>Lokasi Penyaluran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($penjualans as $penjualan)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $penjualan->nomor_kuitansi }}</td>
                                        <td>{{ $penjualan->pembeli }}</td>
                                        <td>{{ $penjualan->sektor->nama }}</td>
                                        <td>{{ $penjualan->jenisBbm->nama }}</td>
                                        <td class="text-end">{{ number_format($penjualan->volume, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($penjualan->dpp, 2, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($penjualan->pbbkb_sistem, 2, ',', '.') }}</td>
                                        <td>{{ Carbon\Carbon::parse($penjualan->tanggal)->format('d/m/Y') }}</td>
                                        <td>{{ $penjualan->lokasi_penyaluran }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        $('#penjualan-table').DataTable({
            "language": {
                "url": '{{ asset("assets/vendors/datatables-lang-id.json") }}'
            }
        });
    </script>
@endpush
