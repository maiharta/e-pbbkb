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
                <div class="col-6 col-lg-4 col-md-6">
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
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-4 col-md-6">
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
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-4 col-md-6">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-3 col-lg-12 col-xl-12 col-xxl-4 d-flex justify-content-start ">
                                    <div class="stats-icon bg-primary mb-2">
                                        <i class="isax isax-money"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total DPP Penjualan</h6>
                                    <h6 class="font-extrabold mb-0">Rp.
                                        {{ number_format($pelaporan->penjualan->sum('dpp'), 2, ',', '.') }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if ($pelaporan->catatan_revisi)
                <div class="card w-100">
                    <div class="card-body d-flex bg-danger text-white align-items-center gap-3">
                        <div class="d-flex gap-3 align-items-center flex-column border-end pe-4">
                            <span class="fw-bold fs-4 isax isax-warning-2"></span>
                            <p class="fs-6 fw-bold mb-0 text-center">Revisi Sebelumnya</p>
                        </div>
                        <p class="mb-0">{{ $pelaporan->catatan_revisi }}</p>
                    </div>
                </div>
            @endif
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
                            <table class="table table-striped table-bordered w-100"
                                   id="pembelian-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Penjual</th>
                                        <th>Kab/Kota</th>
                                        <th>Jenis BBM</th>
                                        <th>Subsidi/Non Subsidi</th>
                                        <th>Total Volume (liter)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pelaporan->pembelian as $pembelian)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $pembelian->penjual }}</td>
                                            <td>{{ $pembelian->kabupaten->nama }}</td>
                                            <td>{{ $pembelian->jenisBbm->nama }}</td>
                                            <td>{{ $pembelian->jenisBbm->is_subsidi ? 'Subsidi' : 'Non Subsidi' }}</td>
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
                            <table class="table table-striped table-bordered w-100"
                                   id="penjualan-table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Pembeli</th>
                                        <th>Kab/Kota</th>
                                        <th>Jenis BBM</th>
                                        <th>Subsidi/Non Subsidi</th>
                                        <th>Sektor</th>
                                        <th>Total Volume (liter)</th>
                                        <th>Total DPP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pelaporan->penjualan as $penjualan)
                                        {{-- @dd($penjualan) --}}
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $penjualan->pembeli }}</td>
                                            <td>{{ $penjualan->kabupaten->nama }}</td>
                                            <td>{{ $penjualan->jenisBbm->nama }}</td>
                                            <td>{{ $penjualan->jenisBbm->is_subsidi ? 'Subsidi' : 'Non Subsidi' }}</td>
                                            <td>{{ $penjualan->sektor->nama }}</td>
                                            <td class="text-start">{{ number_format($penjualan->volume, 0, ',', '.') }}
                                            </td>
                                            <td class="text-start">Rp. {{ number_format($penjualan->dpp, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
        $('#pembelian-table').DataTable({
            "responsive": true,
            "language": {
                "url": '{{ asset('assets/vendors/datatables-lang-id.json') }}'
            }
        });
        $('#penjualan-table').DataTable({
            "responsive": true,
            "language": {
                "url": '{{ asset('assets/vendors/datatables-lang-id.json') }}'
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
            Swal.fire({
                icon: 'question',
                title: 'Apakah anda yakin ingin menyetujui permohonan ini?',
                showCancelButton: true,
                confirmButtonText: `Ya`,
                cancelButtonText: `Tidak`,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('verifikasi.pelaporan.approve') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            ulid: getUlid(),
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
            });
        }
    </script>
@endpush
