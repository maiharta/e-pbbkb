@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6">
                    <h3>Data Pembelian</h3>
                    <p class="text-muted">{{ $pelaporan->user->name }} - {{ $pelaporan->bulan_name }} {{ $pelaporan->tahun }}</p>
                </div>
                <div class="col-12 col-md-6">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('laporan.penginputan.index') }}">Laporan Penginputan</a></li>
                            <li class="breadcrumb-item active">Data Pembelian</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="pembelian-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Penjual</th>
                                    <th>Jenis BBM</th>
                                    <th>Volume (Liter)</th>
                                    <th>Sisa Volume (Liter)</th>
                                    <th>Nomor Kuitansi</th>
                                    <th>Tanggal</th>
                                    <th>Alamat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pembelians as $pembelian)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $pembelian->penjual }}</td>
                                        <td>{{ $pembelian->nama_jenis_bbm }}</td>
                                        <td class="text-end">{{ number_format($pembelian->volume, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($pembelian->sisa_volume, 0, ',', '.') }}</td>
                                        <td>{{ $pembelian->nomor_kuitansi }}</td>
                                        <td>{{ Carbon\Carbon::parse($pembelian->tanggal)->format('d/m/Y') }}</td>
                                        <td>{{ $pembelian->alamat }}</td>
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
        $('#pembelian-table').DataTable({
            "language": {
                "url": '{{ asset("assets/vendors/datatables-lang-id.json") }}'
            },
        });
    </script>
@endpush
