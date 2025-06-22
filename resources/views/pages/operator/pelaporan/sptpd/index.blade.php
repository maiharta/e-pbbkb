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

        .btn-action:hover {
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-primary {
            background-color: #435ebe;
            border-color: #435ebe;
        }

        .btn-icon {
            margin-right: 0.5rem;
        }

        .modal-content {
            border-radius: 10px;
            border: none;
        }

        .modal-header {
            background-color: rgba(67, 94, 190, 0.05);
            border-bottom: 1px solid rgba(67, 94, 190, 0.1);
        }

        .modal-footer {
            background-color: rgba(67, 94, 190, 0.02);
            border-top: 1px solid rgba(67, 94, 190, 0.1);
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
                            <li class="breadcrumb-item"><a href="{{ route('pelaporan.index') }}">Pelaporan</a>
                            </li>
                            <li aria-current="page"
                                class="breadcrumb-item active">SPTPD - Data Penjualan {{ $pelaporan->bulan_name }}
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
                            <td>Periode Laporan</td>
                            <td>: {{ $pelaporan->bulan_name }} {{ $pelaporan->tahun }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal Approval SPTPD</td>
                            <td>: {{ Carbon\Carbon::parse($pelaporan->sptpd_approved_at)->locale('id')->isoFormat('LL') }}
                            </td>
                        </tr>
                        <tr>
                            <td>Nomor SPTPD</td>
                            <td>: {{ $pelaporan->sptpd_number }}</td>
                        </tr>
                        <tr>
                            <td>NPWPD</td>
                            <td>: {{ $pelaporan->user->userDetail->npwpd }}</td>
                        </tr>
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

                    @if (!$pelaporan->is_sptpd_approved)
                        <div class="d-flex gap-3 mt-4">
                            <button class="btn btn-secondary btn-action flex-grow-1"
                                    onclick="cancelSptpd('{{ $pelaporan->ulid }}')">
                                <i class="bi bi-arrow-left-square btn-icon"></i> Perbaiki Data
                            </button>
                            <button class="btn btn-primary btn-action flex-grow-1"
                                    data-bs-target="#staticBackdrop"
                                    data-bs-toggle="modal">
                                <i class="bi bi-file-earmark-check btn-icon"></i> Surat Pernyataan
                            </button>
                        </div>
                    @else
                        {{-- download sptpd button --}}
                        <div class="d-flex justify-content-center mt-4">
                            <a class="btn btn-primary"
                               href="{{ route('pelaporan.sptpd.download', $pelaporan->ulid) }}"
                               target="_blank">
                                <i class="isax isax-document"></i>
                                Unduh Dokumen SPTPD
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statement Modal -->
            <div aria-hidden="true"
                 aria-labelledby="staticBackdropLabel"
                 class="modal fade"
                 data-bs-backdrop="static"
                 data-bs-keyboard="false"
                 id="staticBackdrop"
                 tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"
                                id="staticBackdropLabel">
                                <i class="bi bi-file-earmark-text me-2"></i>Pernyataan SPTPD
                            </h5>
                            <button aria-label="Close"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    type="button"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-light border mb-4">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-info-circle-fill text-primary fs-4 me-3"></i>
                                    <p class="mb-0">Silahkan konfirmasi data pada form di bawah ini untuk melanjutkan
                                        proses SPTPD</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold"
                                               for="periode">Periode</label>
                                        <input class="form-control bg-light"
                                               id="periode"
                                               name="periode"
                                               readonly
                                               type="text"
                                               value="{{ $pelaporan->bulan_name }} - {{ $pelaporan->tahun }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold"
                                               for="pbbkb">Jumlah Pemungutan PBBKB</label>
                                        <input class="form-control bg-light"
                                               id="pbbkb"
                                               name="pbbkb"
                                               readonly
                                               type="text"
                                               value="Rp {{ number_format($pelaporan->data_formatted->values()->map(fn($item) => $item->values()->pluck('subtotal')->sum('pbbkb'))->sum(), 2, ',', '.') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label fw-bold"
                                       for="wajib_pungut">Wajib Pungut</label>
                                <input class="form-control bg-light"
                                       id="wajib_pungut"
                                       name="wajib_pungut"
                                       readonly
                                       type="text"
                                       value="{{ auth()->user()->name }}">
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label fw-bold"
                                       for="npwpd">NPWPD</label>
                                <input class="form-control bg-light"
                                       id="npwpd"
                                       name="npwpd"
                                       readonly
                                       type="text"
                                       value="{{ auth()->user()->userDetail->npwpd }}">
                            </div>

                            <div class="alert alert-danger mt-4 mb-0">
                                <div class="d-flex">
                                    <i class="bi bi-exclamation-triangle-fill me-3 fs-5"></i>
                                    <p class="mb-0"><strong>Perhatian:</strong> Dengan menyadari sepenuhnya akan segala
                                        akibat termasuk sanksi sesuai dengan ketentuan perundang-undangan yang berlaku, saya
                                        atau yang saya beri kuasa menyatakan bahwa apa yang telah kami beritahu tersebut
                                        beserta lampiran-lampirannya adalah benar, lengkap, dan jelas.</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-outline-secondary"
                                    data-bs-dismiss="modal"
                                    type="button">
                                <i class="bi bi-x-circle me-1"></i> Batal
                            </button>
                            <button class="btn btn-primary"
                                    onclick="approveSptpd('{{ $pelaporan->ulid }}')"
                                    type="button">
                                <i class="bi bi-check-circle me-1"></i> Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        function cancelSptpd(ulid) {
            Swal.fire({
                'title': 'Apakah anda yakin?',
                'text': 'Anda akan membatalkan proses SPTPD dan memperbaiki data pelaporan kemudian melakukan verifikasi ulang ke admin',
                'icon': 'warning',
                'showCancelButton': true,
                'confirmButtonText': 'Ya',
                'cancelButtonText': 'Batal',
                'customClass': {
                    confirmButton: 'btn btn-secondary me-2',
                    cancelButton: 'btn btn-outline-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // ajax
                    $.ajax({
                        'url': '{{ route('pelaporan.sptpd.cancel') }}/' + ulid,
                        'type': 'POST',
                        'data': {
                            '_token': '{{ csrf_token() }}'
                        },
                        'success': function(data) {
                            if (data.status == 'success') {
                                Swal.fire({
                                    'title': 'Berhasil',
                                    'text': data.message,
                                    'icon': 'success',
                                    'showConfirmButton': false,
                                    'allowOutsideClick': false,
                                    'timer': 1500,
                                }).then(function() {
                                    window.location.href = '{{ route('pelaporan.index') }}'
                                });
                            }
                        },
                        'error': function(data) {
                            Swal.fire({
                                'title': 'Gagal',
                                'text': data.message,
                                'icon': 'error',
                                'showConfirmButton': false,
                                'timer': 1500,
                            })
                        }
                    });
                }
            })
        }

        function approveSptpd(ulid) {
            Swal.fire({
                'title': 'Apakah anda yakin?',
                'showCancelButton': true,
                'confirmButtonText': 'Ya, Simpan',
                'cancelButtonText': 'Batal',
                'reverseButtons': true,
                'customClass': {
                    confirmButton: 'btn btn-primary ms-2',
                    cancelButton: 'btn btn-outline-secondary'
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    // ajax
                    $.ajax({
                        'url': '{{ route('pelaporan.sptpd.approve') }}/' + ulid,
                        'type': 'POST',
                        'data': {
                            '_token': '{{ csrf_token() }}'
                        },
                        'success': function(data) {
                            if (data.status == 'success') {
                                Swal.fire({
                                    'title': 'Berhasil',
                                    'text': data.message,
                                    'icon': 'success',
                                    'showConfirmButton': false,
                                    'allowOutsideClick': false,
                                    'timer': 1500,
                                }).then(function() {
                                    window.location.href = '{{ route('pelaporan.index') }}'
                                });
                            }
                        },
                        'error': function(data) {
                            Swal.fire({
                                'title': 'Gagal',
                                'text': data.message,
                                'icon': 'error',
                                'showConfirmButton': false,
                                'timer': 1500,
                            })
                        }
                    });
                }
            })
        }
    </script>
@endpush
