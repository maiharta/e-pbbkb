@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6">
                    <h3>Data Penjualan</h3>
                    <p class="text-muted">{{ $pelaporan->user->name }} - {{ $pelaporan->bulan_name }} {{ $pelaporan->tahun }}
                    </p>
                </div>
                <div class="col-12 col-md-6">
                    <nav aria-label="breadcrumb"
                         class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('laporan.penginputan.index') }}">Laporan
                                    Penginputan</a></li>
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
                        <table class="table table-striped table-hover"
                               id="penjualan-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Pembeli</th>
                                    <th>Nomor Kuitansi</th>
                                    <th>Tanggal</th>
                                    <th>Jenis BBM</th>
                                    <th>Sektor</th>
                                    <th>Total Volume (liter)</th>
                                    <th>DPP</th>
                                    <th>Status Pajak</th>
                                    <th>PBBKB User</th>
                                    <th>PBBKB Sistem</th>
                                </tr>
                            </thead>
                            <tbody>
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
            "responsive": true,
            "processing": true,
            "serverSide": true,
            ajax: {
                url: '{{ route('verifikasi.pelaporan.penjualan.table', $pelaporan->ulid) }}',
                data: function(d) {
                    d.search = $('#dt-search-0').val();
                }
            },
            "language": {
                "url": '{{ asset('assets/vendors/datatables-lang-id.json') }}'
            },
            "columns": [{
                    data: null,
                    name: 'index',
                    searchable: false,
                    orderable: false,
                    className: 'text-center',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'pembeli',
                    name: 'pembeli',
                    orderable: false
                },
                {
                    data: 'nomor_kuitansi',
                    name: 'nomor_kuitansi'
                },
                {
                    data: 'tanggal',
                    name: 'tanggal'
                },
                {
                    data: 'jenis_bbm',
                    name: 'jenis_bbm'
                },
                {
                    data: 'sektor',
                    name: 'sektor'
                },
                {
                    data: 'volume',
                    name: 'volume',
                    className: 'text-end'
                },
                {
                    data: 'dpp',
                    name: 'dpp',
                    className: 'text-end'
                },
                {
                    data: "is_wajib_pajak",
                    className: "text-start"
                },
                {
                    data: "pbbkb",
                    className: "text-start"
                },
                {
                    data: "pbbkb_sistem",
                    className: "text-start"
                },
            ],
        });
    </script>
@endpush
