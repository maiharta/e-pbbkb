@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12">
                    <h3>Master Data Cuti</h3>
                </div>
            </div>
        </div>
        <section class="section">
            <a class="btn btn-primary mb-3"
               href="{{ route('master-data.cuti.create') }}">+ Tambah Data</a>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover"
                               id="cuti-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th class="text-start">Tanggal</th>
                                    <th class="text-start">Deskripsi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cutis as $cuti)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-start">{{ $cuti->tanggal_formatted }}</td>
                                        <td class="text-start">{{ $cuti->deskripsi }}</td>
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
                                                           href="{{ route('master-data.cuti.edit', $cuti->ulid) }}">Edit</a>
                                                    </li>
                                                    <li><a class="dropdown-item text-danger delete-item"
                                                           href="javascript:void(0)"
                                                           data-id="{{ $cuti->ulid }}"
                                                           data-tanggal="{{ $cuti->tanggal_formatted }}">Hapus</a>
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
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            const dataTable = $('#cuti-table').DataTable({
                "responsive": true,
                "language": {
                    "url": '{{ asset('assets/vendors/datatables-lang-id.json') }}'
                }
            });

            // Handle Delete Button Click
            $(document).on('click', '.delete-item', function() {
                const id = $(this).data('id');
                const tanggal = $(this).data('tanggal');

                // Show confirmation using SweetAlert2
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    html: `Data cuti pada tanggal <strong>${tanggal}</strong> akan dihapus dan tidak dapat dikembalikan!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading state
                        Swal.fire({
                            title: 'Memproses...',
                            html: 'Mohon tunggu sebentar.',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Send AJAX request
                        $.ajax({
                            url: `{{ url('master-data/cuti') }}/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    // Show success message
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: response.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        // Reload the page to refresh the table
                                        location.reload();
                                    });
                                } else {
                                    // Show error message
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal!',
                                        text: response.message || 'Terjadi kesalahan saat menghapus data.'
                                    });
                                }
                            },
                            error: function(xhr) {
                                // Parse error response
                                let errorMessage = 'Terjadi kesalahan pada sistem.';

                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }

                                // Show error message
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: errorMessage
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
