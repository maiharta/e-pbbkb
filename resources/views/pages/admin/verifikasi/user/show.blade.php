@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Verifikasi User</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb"
                         class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('verifikasi.user.index') }}">Verifikasi User</a>
                            </li>
                            <li aria-current="page"
                                class="breadcrumb-item active">{{ $user->email }}</li>
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
                            <th>: {{ $user->name }}</th>
                        </tr>
                        <tr>
                            <td class="pb-1 pe-4">NPWPD</td>
                            <th>: {{ $user->userDetail->npwpd }}</th>
                        </tr>
                        <tr>
                            <td class="pb-1 pe-4">Nomor Telepon</td>
                            <th>: {{ $user->userDetail->nomor_telepon }}</th>
                        </tr>
                        <tr>
                            <td class="pb-1 pe-4">Kabupaten/Kota</td>
                            <th>: {{ $user->userDetail->kabupaten->nama }}</th>
                        </tr>
                        <tr>
                            <td class="pb-1 pe-4">Alamat</td>
                            <th>: {{ $user->userDetail->alamat }}</th>
                        </tr>
                        <tr>
                            <td class="pb-1 pe-4">Tanggal Pengajuan</td>
                            <th>:
                                {{ Carbon\Carbon::parse($user->userDetail->created_at)->locale('id')->isoFormat('LL') }}
                            </th>
                        </tr>
                        <tr>
                            <td class="pb-1 pe-4">File Berkas Persyaratan</td>
                            <th>:
                                <a class="text-decoration-underline"
                                   href="{{ Storage::url($user->userDetail->filepath_berkas_persyaratan) }}"
                                   target="_blank">Lihat Berkas</a>
                            </th>
                        </tr>
                    </table>
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
        function getUlid() {
            return '{{ $user->ulid }}';
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
                                url: "{{ route('verifikasi.user.revisi') }}",
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
                                                "{{ route('verifikasi.user.index') }}";
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
                                        'Form permohonan gagal direvisi.',
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
                        url: "{{ route('verifikasi.user.approve') }}",
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
                                        "{{ route('verifikasi.user.index') }}";
                                });
                            } else {
                                Toast.fire({
                                    icon: 'error',
                                    title: response.message,
                                });
                            }
                        },
                        error: function(response) {
                            Toast.fire({
                                icon: 'error',
                                title: response.message,
                            });
                        }
                    });
                }
            });
        }
    </script>
@endpush
