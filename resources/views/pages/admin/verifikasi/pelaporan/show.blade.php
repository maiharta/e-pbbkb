@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Verifikasi Pelaporan</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb"
                         class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('verifikasi.pelaporan.index') }}">Verifikasi
                                    Pelaporan</a>
                            </li>
                            <li aria-current="page"
                                class="breadcrumb-item active">{{ $pelaporan->user->name }} - {{ $pelaporan->bulan_name }}
                                {{ $pelaporan->tahun }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <table>
                        <tr>
                            <td class="pb-1 pe-4">Nama perusahaan</td>
                            <th>: {{ $pelaporan->user->name }}</th>
                        </tr>
                        <tr>
                            <td class="pb-1 pe-4">Periode Laporan</td>
                            <th>: {{ $pelaporan->bulan_name }} {{ $pelaporan->tahun }}</th>
                        </tr>
                        <tr>
                            <td class="pb-1 pe-4">Tanggal Pengajuan</td>
                            <th>:
                                {{ Carbon\Carbon::parse($pelaporan->created_at)->locale('id')->isoFormat('LL') }}
                            </th>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-6 col-lg-4 col-md-4">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-3 col-lg-12 col-xl-12 col-xxl-4 d-flex justify-content-start ">
                                    <div class="stats-icon bg-primary mb-2">
                                        <i class="isax isax-money"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold text-sm">Total DPP Penjualan</h6>
                                    <h6 class="font-extrabold mb-0">Rp.
                                        {{ number_format($pelaporan->penjualan->sum('dpp'), 2, ',', '.') }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-4 col-md-4">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-3 col-lg-12 col-xl-12 col-xxl-4 d-flex justify-content-start ">
                                    <div class="stats-icon bg-primary mb-2">
                                        <i class="isax isax-money"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold text-sm">Total PBBKB User</h6>
                                    <h6 class="font-extrabold mb-0">Rp.
                                        {{ number_format($pelaporan->penjualan->sum('pbbkb'), 2, ',', '.') }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-4 col-md-4">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-3 col-lg-12 col-xl-12 col-xxl-4 d-flex justify-content-start ">
                                    <div class="stats-icon bg-primary mb-2">
                                        <i class="isax isax-money"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold text-sm">Total PBBKB Sistem</h6>
                                    <h6 class="font-extrabold mb-0">Rp.
                                        {{ number_format($pelaporan->penjualan->sum('pbbkb_sistem'), 2, ',', '.') }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-3 col-lg-12 col-xl-12 col-xxl-4 d-flex justify-content-start ">
                                    <div class="stats-icon bg-primary mb-2">
                                        <i class="isax isax-document-download"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Volume Pembelian</h6>
                                    <h6 class="font-extrabold mb-0">
                                        {{ number_format($pelaporan->pembelian->sum('volume'), 0, ',', '.') }}</h6>
                                </div>
                            </div>
                            <hr>
                            <div class="mt-3"
                                 style="max-height: 300px; overflow-y: auto">
                                <table class="table">
                                    <tr>
                                        <th>Jenis PBBKB</th>
                                        <th>Sisa Terakhir(L)</th>
                                        <th>Volume(L)</th>
                                    </tr>
                                    @foreach ($pelaporan->data_pembelian_terakhir as $pembelian)
                                        <tr>
                                            <td>{{ $pembelian->nama_jenis_bbm }}</td>
                                            <td>{{ number_format($pembelian->sisa_volume, 0, ',', '.') }}</td>
                                            <td>{{ number_format($pembelian->total_volume, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-3 col-lg-12 col-xl-12 col-xxl-4 d-flex justify-content-start ">
                                    <div class="stats-icon bg-primary mb-2">
                                        <i class="isax isax-document-upload"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Volume Penjualan</h6>
                                    <h6 class="font-extrabold mb-0">
                                        {{ number_format($pelaporan->penjualan->sum('volume'), 0, ',', '.') }}</h6>
                                </div>
                            </div>
                            <hr>
                            <div class="mt-3"
                                 style="max-height: 300px; overflow-y: auto">
                                <table class="table">
                                    <tr>
                                        <th>Jenis PBBKB</th>
                                        <th>Volume (L)</th>
                                    </tr>
                                    @foreach ($pelaporan->data_penjualan_terakhir as $penjualan)
                                        <tr>
                                            <td>{{ $penjualan->nama_jenis_bbm }}</td>
                                            <td>{{ number_format($penjualan->total_volume, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if ($pelaporan->catatan_revisi)
                <div class="card w-100">
                    <div
                         class="card-body d-flex {{ $pelaporan->is_sptpd_canceled ? 'bg-info' : 'bg-danger' }} text-white align-items-center gap-3">
                        <div class="d-flex gap-3 align-items-center flex-column border-end pe-4">
                            @if ($pelaporan->is_sptpd_canceled)
                                <span class="fw-bold fs-4 isax isax-warning-2"></span>
                                <p class="fs-6 fw-bold mb-0">Info</p>
                            @else
                                <span class="fw-bold fs-4 isax isax-warning-2"></span>
                                <p class="fs-6 fw-bold mb-0 text-center">Revisi Sebelumnya</p>
                            @endif
                        </div>
                        <p class="mb-0">{{ $pelaporan->catatan_revisi }}</p>
                    </div>
                </div>
            @endif
            <button aria-controls="collapseNote"
                    aria-expanded="false"
                    class="btn btn-primary w-100 mb-3 fw-bold"
                    data-bs-target="#collapseNote"
                    data-bs-toggle="collapse"
                    type="button">
                <span class="isax isax-danger text-danger"></span>
                {{ $pelaporan->pelaporanNote->where('is_active', true)->where('status', 'danger')->count() }}
                <span class="isax isax-warning-2 text-warning"></span>
                {{ $pelaporan->pelaporanNote->where('is_active', true)->where('status', 'info')->count() }}
                <span class="ms-4">
                    Klik
                    untuk
                    menampilkan
                    catatan sistem
                </span>
            </button>
            <div class="collapse"
                 id="collapseNote">
                <div class="card card-body text-sm">
                    @if ($pelaporan->pelaporanNote->count() == 0)
                        Tidak ada catatan sistem
                    @endif
                    @foreach ($pelaporan->pelaporanNote->where('is_active', true)->where('status', 'danger') as $pelaporan_note)
                        <p class="mb-2"><span
                                  class="isax isax-danger text-danger me-2"></span>{{ $pelaporan_note->penjualan->nomor_kuitansi }}
                            - {{ $pelaporan_note->deskripsi }}
                        </p>
                    @endforeach
                    <hr class="mt-0 mb-2">
                    @foreach ($pelaporan->pelaporanNote->where('is_active', true)->where('status', 'info') as $pelaporan_note)
                        <p class="mb-2"><span
                                  class="isax isax-warning-2 text-warning me-2"></span>{{ $pelaporan_note->deskripsi }}
                        </p>
                    @endforeach
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs"
                        id="myTab"
                        role="tablist">
                        <li class="nav-item"
                            role="presentation">
                            <a aria-controls="pembelian"
                               aria-selected="true"
                               class="nav-link active"
                               data-bs-toggle="tab"
                               href="#pembelian"
                               id="pembelian-tab"
                               role="tab">Data Pembelian</a>
                        </li>
                        <li class="nav-item"
                            role="presentation">
                            <a aria-controls="penjualan"
                               aria-selected="false"
                               class="nav-link"
                               data-bs-toggle="tab"
                               href="#penjualan"
                               id="penjualan-tab"
                               role="tab"
                               tabindex="-1">Data Penjualan</a>
                        </li>
                    </ul>
                    <div class="tab-content mt-2 border-top pt-3"
                         id="myTabContent">
                        <div aria-labelledby="pembelian-tab"
                             class="tab-pane fade active show"
                             id="pembelian"
                             role="tabpanel">
                            <table class="table text-sm table-striped table-bordered w-100"
                                   id="pembelian-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Penjual</th>
                                        <th>Nomor Kuitansi</th>
                                        <th>Tanggal</th>
                                        <th>Jenis BBM</th>
                                        <th>Sisa Volume (liter)</th>
                                        <th>Total Volume (liter)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pelaporan->pembelian as $pembelian)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $pembelian->penjual }}</td>
                                            <td>{{ $pembelian->nomor_kuitansi }}</td>
                                            <td>{{ $pembelian->tanggal_formatted }}</td>
                                            <td>{{ $pembelian->jenisBbm->nama }} -
                                                {{ $pembelian->jenisBbm->is_subsidi ? 'Subsidi' : 'Non Subsidi' }}</td>
                                            <td class="text-start">
                                                {{ number_format($pembelian->sisa_volume, 0, ',', '.') }}
                                            </td>
                                            <td class="text-start">{{ number_format($pembelian->volume, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div aria-labelledby="penjualan-tab"
                             class="tab-pane fade"
                             id="penjualan"
                             role="tabpanel">
                            <div class="table-responsive">
                                <table class="table text-sm table-bordered w-100"
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
                                            <th>Total DPP</th>
                                            <th>Status Pajak</th>
                                            <th>PBBKB User</th>
                                            <th>PBBKB Sistem</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-3 justify-content-end">
                <button class="btn btn-danger"
                        onclick="revisi()"><i class="isax isax-warning-2 me-2"></i>Revisi</button>
                <button class="btn btn-primary"
                        onclick="valid()"><i class="isax isax-tick-circle me-2"></i>Setujui</button>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        var table = $('#pembelian-table').DataTable({
            "responsive": true,
            "language": {
                "url": '{{ asset('assets/vendors/datatables-lang-id.json') }}'
            }
        });
        var table2 = $('#penjualan-table').DataTable({
            "responsive": true,
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('verifikasi.pelaporan.penjualan.table', $pelaporan->ulid) }}',
                data: function(d) {
                    d.search = $('#penjualan #dt-search-1').val();
                }
            },
            columns: [{
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
                    orderable: false,
                },
                {
                    data: 'nomor_kuitansi',
                    name: 'nomor_kuitansi',
                    orderable: false,
                },
                {
                    data: 'tanggal',
                    name: 'tanggal',
                    searchable: false
                },
                {
                    data: 'jenis_bbm',
                    name: 'jenis_bbm',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'sektor',
                    name: 'sektor',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'volume',
                    name: 'volume',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'dpp',
                    name: 'dpp',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'is_wajib_pajak',
                    name: 'is_wajib_pajak',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'pbbkb',
                    name: 'pbbkb',
                    orderable: false,
                    searchable: false,
                    className: 'text-end'
                },
                {
                    data: 'pbbkb_sistem',
                    name: 'pbbkb_sistem',
                    orderable: false,
                    searchable: false,
                    className: 'text-end'
                }
            ],
            "language": {
                "url": '{{ asset('assets/vendors/datatables-lang-id.json') }}'
            },
            "createdRow": function(row, data, dataIndex) {
                // Add background color to rows where is_pbbkb_match is false
                if (!data.is_pbbkb_match) {
                    $(row).addClass('bg-danger text-white');
                }
            }
        });

        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            if ($(e.target).attr('id') === 'penjualan-tab') {
                table2.ajax.reload();
            }
        });
    </script>
    <script>
        function getUlid() {
            return '{{ $pelaporan->ulid }}';
        }

        function revisi() {
            Swal.fire({
                icon: 'question',
                title: 'Apakah anda yakin ingin merevisi data ini?',
                showCancelButton: true,
                confirmButtonText: `Ya`,
                cancelButtonText: `Tidak`,
            }).then((result) => {
                if (result.isConfirmed) {
                    // Swal fire masuukan keterangan revisi
                    Swal.fire({
                        icon: 'warning',
                        title: 'Masukkan keterangan revisi',
                        input: 'textarea',
                        // input required
                        inputAttributes: {
                            required: true
                        },
                        inputValidator: (value) => {
                            if (!value) {
                                return 'Keterangan revisi tidak boleh kosong!'
                            }
                        },
                        showCancelButton: true,
                        confirmButtonText: `Kirim`,
                        cancelButtonText: `Batal`,
                        preConfirm: (catatan_revisi) => {
                            $.ajax({
                                url: "{{ route('verifikasi.pelaporan.revisi') }}",
                                type: "POST",
                                data: {
                                    "_token": "{{ csrf_token() }}",
                                    "ulid": getUlid(),
                                    "catatan_revisi": catatan_revisi
                                },
                                success: function(response) {
                                    if (response.status == 'success') {
                                        Swal.fire(
                                            'Berhasil!',
                                            response.message,
                                            'success'
                                        ).then((result) => {
                                            window.location.href =
                                                "{{ route('verifikasi.pelaporan.index') }}";
                                        });
                                    } else {
                                        Swal.fire(
                                            'Gagal!',
                                            response.message,
                                            'error'
                                        );
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire(
                                        'Gagal!',
                                        'Form permohonan gagal direvisi. Hubungi administrator',
                                        'error'
                                    );
                                }
                            });
                        }
                    })
                }
            });
        }

        function valid() {
            @if ($pelaporan->pelaporanNote->where('is_active', true)->where('status', 'danger')->count() != 0)
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal menyetujui laporan. Terdapat data yang belum sesuai dengan sistem'
                });
            @else
                Swal.fire({
                    icon: 'question',
                    title: 'Apakah anda yakin ingin menyetujui permohonan ini?',
                    showCancelButton: true,
                    confirmButtonText: `Ya`,
                    cancelButtonText: `Tidak`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Masukkan Nomor SPTPD',
                            input: 'text',
                            // input required
                            inputAttributes: {
                                required: true
                            },
                            inputValidator: (value) => {
                                if (!value) {
                                    return 'Nomor SPTPD tidak boleh kosong!'
                                }
                            },
                            showCancelButton: true,
                            confirmButtonText: `Kirim`,
                            cancelButtonText: `Batal`,
                            preConfirm: (nomor_sptpd) => {
                                $.ajax({
                                    url: "{{ route('verifikasi.pelaporan.approve') }}",
                                    type: "POST",
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        ulid: getUlid(),
                                        nomor_sptpd: nomor_sptpd
                                    },
                                    success: function(response) {
                                        if (response.status == 'success') {
                                            Swal.fire(
                                                'Berhasil!',
                                                response.message,
                                                'success'
                                            ).then((result) => {
                                                window.location.href =
                                                    "{{ route('verifikasi.pelaporan.index') }}";
                                            });
                                        } else {
                                            Toast.fire({
                                                icon: 'error',
                                                title: response.message,
                                            });
                                        }
                                    },
                                    error: function(response) {
                                        Swal.fire(
                                            'Gagal!',
                                            'Form permohonan gagal divalidasi. Hubungi administrator',
                                            'error'
                                        );
                                    }
                                });
                            }
                        })
                    }
                });
            @endif
        }
    </script>
@endpush
