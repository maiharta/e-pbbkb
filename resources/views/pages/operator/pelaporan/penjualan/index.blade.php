@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Data Penjualan</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb"
                         class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('pelaporan.index') }}">Pelaporan</a>
                            </li>
                            <li aria-current="page"
                                class="breadcrumb-item active">Data Penjualan {{ $pelaporan->bulan_name }}
                                {{ $pelaporan->tahun }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            @if ($pelaporan->catatan_revisi)
                <div class="card w-100">
                    <div
                         class="card-body d-flex {{ $pelaporan->is_sptpd_canceled ? 'bg-info' : 'bg-danger' }} text-white align-items-center gap-3">
                        <div class="d-flex gap-2 align-items-center flex-column border-end pe-4">
                            @if ($pelaporan->is_sptpd_canceled)
                                <span class="fw-bold fs-4 isax isax-warning-2"></span>
                                <p class="fs-6 fw-bold mb-0">Info</p>
                            @else
                                <span class="fw-bold fs-4 isax isax-warning-2"></span>
                                <p class="fs-6 fw-bold mb-0">Revisi</p>
                            @endif
                        </div>
                        <p class="mb-0">{{ $pelaporan->catatan_revisi }}</p>
                    </div>
                </div>
            @endif

            {{-- card --}}
            <div class="row mb-2">
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card dashboard-card h-80">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="card-icon bg-soft-success">
                                    <i class="isax isax-people fs-3 text-primary"></i>
                                </div>
                            </div>
                            <h5 class="text-value"
                                id="totalPelaporan">{{ $transaction_total }}</h5>
                            <p class="text-label mb-0">Total Penjualan</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card dashboard-card h-80">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="card-icon bg-soft-primary">
                                    <i class="isax isax-money fs-3 text-primary"></i>
                                </div>
                            </div>
                            <h5 class="text-value"
                                id="totalPBBKB">{{ $volume_total }}</h5>
                            <p class="text-label mb-0">Total Volume</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card dashboard-card h-80">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="card-icon bg-soft-warning">
                                    <i class="isax isax-verify fs-3 text-primary"></i>
                                </div>
                            </div>
                            <h5 class="text-value"
                                id="pendingPelaporan">{{ $dpp_total }}</h5>
                            <p class="text-label mb-0">Total DPP</p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card dashboard-card h-80">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="card-icon bg-soft-danger">
                                    <i class="isax isax-clock fs-3 text-primary"></i>
                                </div>
                            </div>
                            <h5 class="text-value"
                                id="revisedPelaporan">{{ $pbbkb_total }}</h5>
                            <p class="text-label mb-0">Total PBBKB</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 align-items-center mb-3">
                @if (!$pelaporan->is_sent_to_admin)
                    <a class="btn btn-primary"
                       href="{{ route('pelaporan.penjualan.create', $pelaporan->ulid) }}">+ Tambah Data</a>
                    <button class="btn btn-primary"
                            data-bs-target="#importModal"
                            data-bs-toggle="modal"
                            type="button">
                        <i class="isax isax-import"></i>Import Data
                    </button>
                    @if ($transaction_total > 0)
                        <button class="btn btn-danger"
                                data-bs-target="#resetModal"
                                data-bs-toggle="modal"
                                type="button">
                            <i class="isax isax-trash"></i>Reset Data Penjualan
                        </button>
                    @endif
                @endif
            </div>
            <div class="card">
                <div class="card-body">
                    {{-- filter --}}
                    <div class="row">
                        <div class="col">
                            <x-input.select :options="$kabupatens->map(fn($item) => ['key' => $item->id, 'value' => $item->nama])"
                                            label="Kabupaten"
                                            name="kabupaten_id"
                                            placeholder="Semua kabupaten"
                                            value="{{ old('kabupaten_id') }}" />
                        </div>
                        <div class="col">
                            <x-input.select :options="$jenis_bbms->map(fn($item) => ['key' => $item->id, 'value' => $item->nama])"
                                            label="Jenis BBM"
                                            name="jenis_bbm_id"
                                            placeholder="Semua jenis BBM"
                                            value="{{ old('jenis_bbm_id') }}" />
                        </div>
                        <div class="col">
                            <x-input.select :options="$sektors->map(fn($item) => ['key' => $item->id, 'value' => $item->nama])"
                                            label="Sektor"
                                            name="sektor_id"
                                            placeholder="Semua sektor"
                                            value="{{ old('sektor_id') }}" />
                        </div>
                    </div>
                    <div class="gap-2 d-flex">
                        <button class="w-100 d-block btn btn-secondary"
                                id="resetFilter"
                                type="button">
                            <i class="bi bi-x-circle me-1"></i> Reset Filter
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered"
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
                                    <th>Harga Per Liter</th>
                                    <th>PBBKB</th>
                                    <th>Aksi</th>
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

    <!-- Modal -->
    <div aria-hidden="true"
         aria-labelledby="importModalLabel"
         class="modal fade"
         data-bs-backdrop="static"
         id="importModal"
         tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="importModalLabel">Import data penjualan</h5>
                    <button aria-label="Close"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            type="button"></button>
                </div>
                <form action="{{ route('pelaporan.penjualan.import', $pelaporan->ulid) }}"
                      enctype="multipart/form-data"
                      method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="file">Pilih file</label>
                            <input class="form-control"
                                   id="file"
                                   name="file"
                                   required=""
                                   type="file">
                        </div>
                        <p class="text-sm">* File wajib bertipe excel. Template struktur excel data diunduh <a
                               class="text-decoration-underline text-primary"
                               href="{{ route('pelaporan.penjualan.download-template-import') }}">Di Sini</a></p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary"
                                data-bs-dismiss="modal"
                                type="button">Close</button>
                        <button class="btn btn-primary"
                                type="submit">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reset Modal -->
    <div aria-hidden="true"
         aria-labelledby="resetModalLabel"
         class="modal fade"
         data-bs-backdrop="static"
         id="resetModal"
         tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="resetModalLabel">Konfirmasi Reset Data</h5>
                    <button aria-label="Close"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                            type="button"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="isax isax-warning-2"></i>
                        <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan.
                    </div>
                    <p>Apakah Anda yakin ingin menghapus <strong>semua data penjualan</strong> pada pelaporan bulan
                        <strong>{{ $pelaporan->bulan_name }} {{ $pelaporan->tahun }}</strong>?
                    </p>
                    <p class="text-danger mb-0">Semua data penjualan akan dihapus secara permanen.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary"
                            data-bs-dismiss="modal"
                            type="button">Batal</button>
                    <button class="btn btn-danger"
                            onclick="resetData()"
                            type="button">
                        <i class="isax isax-trash"></i> Reset Data
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var table = $('#penjualan-table').DataTable({
            "responsive": true,
            "serverSide": true,
            "processing": true,
            ajax: {
                url: '{{ route('pelaporan.penjualan.table', $pelaporan->ulid) }}',
                data: function(d) {
                    d.search = $('#dt-search-0').val();
                    d.kabupaten_id = $('select[name="kabupaten_id"]').val();
                    d.jenis_bbm_id = $('select[name="jenis_bbm_id"]').val();
                    d.sektor_id = $('select[name="sektor_id"]').val();
                }
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
                    data: 'pbbkb',
                    name: 'pbbkb',
                    className: 'text-end'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                },
            ],
            "language": {
                "url": '{{ asset('assets/vendors/datatables-lang-id.json') }}'
            }
        });

        // Filter change handlers
        $('select[name="kabupaten_id"], select[name="jenis_bbm_id"], select[name="sektor_id"]').on('change', function() {
            table.ajax.reload();
        });

        // reset filter
        $('#resetFilter').on('click', function() {
            $('select[name="kabupaten_id"]').val('').trigger('change');
            $('select[name="jenis_bbm_id"]').val('').trigger('change');
            $('select[name="sektor_id"]').val('').trigger('change');
            table.ajax.reload();
        });

        function hapus(route) {
            Swal.fire({
                'title': 'Apakah anda yakin?',
                'text': 'Anda akan menghapus data ini',
                'icon': 'warning',
                'showCancelButton': true,
                'confirmButtonText': 'Hapus',
                'cancelButtonText': 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Mohon tunggu',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // ajax
                    $.ajax({
                        'url': route,
                        'type': 'DELETE',
                        'data': {
                            '_token': '{{ csrf_token() }}'
                        },
                        'success': function(data) {
                            if (data.status == 'success') {
                                Swal.fire({
                                    'title': 'Berhasil',
                                    'text': 'Data berhasil dihapus',
                                    'icon': 'success',
                                    'showConfirmButton': false,
                                    'allowOutsideClick': false,
                                    'timer': 1500,
                                }).then(function() {
                                    window.location.reload();
                                });
                            }
                        },
                        'error': function(data) {
                            Swal.fire({
                                'title': 'Gagal',
                                'text': 'Data gagal dihapus',
                                'icon': 'error',
                                'showConfirmButton': false,
                                'timer': 1500,
                            })
                        }
                    });
                }
            })
        }

        function resetData() {
            // Close modal
            $('#resetModal').modal('hide');

            // Show loading
            Swal.fire({
                'title': 'Menghapus data...',
                'allowOutsideClick': false,
                'allowEscapeKey': false,
                'didOpen': () => {
                    Swal.showLoading();
                }
            });

            // Ajax request
            $.ajax({
                'url': '{{ route('pelaporan.penjualan.reset', $pelaporan->ulid) }}',
                'type': 'DELETE',
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
                            window.location.reload();
                        });
                    }
                },
                'error': function(xhr) {
                    let message = 'Terjadi kesalahan saat menghapus data';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        'title': 'Gagal',
                        'text': message,
                        'icon': 'error',
                        'confirmButtonText': 'OK'
                    });
                }
            });
        }
    </script>
@endpush
