@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Data Pembelian</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb"
                         class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('pelaporan.index') }}">Pelaporan</a>
                            </li>
                            <li aria-current="page"
                                class="breadcrumb-item active">Data Pembelian {{ $pelaporan->bulan_name }}
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
            <div class="d-flex gap-2 align-items-center mb-3">
                <a class="btn btn-primary"
                   href="{{ route('pelaporan.pembelian.create', $pelaporan->ulid) }}">+ Tambah Data</a>
                <button class="btn btn-primary"
                        data-bs-target="#importModal"
                        data-bs-toggle="modal"
                        type="button">
                    <i class="isax isax-import"></i>Import Data
                </button>
            </div>
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped table-bordered"
                           id="pembelian-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Penjual</th>
                                <th>Tanggal</th>
                                <th>Jenis BBM</th>
                                <th>Subsidi/Non Subsidi</th>
                                <th>Sisa Volume (liter)</th>
                                <th>Total Volume (liter)</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pembelians as $pembelian)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $pembelian->penjual }}</td>
                                    <td>{{ $pembelian->tanggal_formatted }}</td>
                                    <td>{{ $pembelian->jenisBbm->nama }}</td>
                                    <td>{{ $pembelian->jenisBbm->is_subsidi ? 'Subsidi' : 'Non Subsidi' }}</td>
                                    <td class="text-start">{{ number_format($pembelian->sisa_volume, 0, ',', '.') }}</td>
                                    <td class="text-start">{{ number_format($pembelian->volume, 0, ',', '.') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button aria-expanded="false"
                                                    class="btn"
                                                    data-bs-toggle="dropdown"
                                                    id="dropdownMenuButton1"
                                                    type="button">
                                                <i class="isax isax-more"></i>
                                            </button>
                                            <ul aria-labelledby="dropdownMenuButton1"
                                                class="dropdown-menu">
                                                <li><a class="dropdown-item"
                                                       href="{{ route('pelaporan.pembelian.edit', ['pembelian' => $pembelian->ulid, 'ulid' => $pelaporan->ulid]) }}">Edit</a>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item"
                                                            onclick="hapus('{{ route('pelaporan.pembelian.destroy', ['pembelian' => $pembelian->ulid, 'ulid' => $pelaporan->ulid]) }}')">
                                                        Hapus
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
                        id="importModalLabel">Import data pembelian</h5>
                    <button aria-label="Close"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            type="button"></button>
                </div>
                <form action="{{ route('pelaporan.pembelian.import', $pelaporan->ulid) }}"
                      enctype="multipart/form-data"
                      method="POST">
                    <div class="modal-body">
                        @csrf
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
                               href="{{ route('pelaporan.pembelian.download-template-import') }}">Di Sini</a></p>
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
@endsection

@push('scripts')
    <script>
        $('#pembelian-table').DataTable({
            "responsive": true,
            "language": {
                "url": '{{ asset('assets/vendors/datatables-lang-id.json') }}'
            }
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
    </script>
@endpush
