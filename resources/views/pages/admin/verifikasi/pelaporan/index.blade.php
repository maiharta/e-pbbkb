@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12">
                    <h3>Verifikasi Pelaporan</h3>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover"
                               id="samsat-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Perusahaan</th>
                                    <th>Periode Laporan</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pelaporans as $pelaporan)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $pelaporan->user->name }}</td>
                                        <td>{{ $pelaporan->bulan_name }} {{ $pelaporan->tahun }}</td>
                                        <td>{{ Carbon\Carbon::parse($pelaporan->created_at)->locale('id')->isoFormat('LL') }}</td>
                                        <td>
                                            {{-- dropdown --}}
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
                                                           href="{{ route('verifikasi.pelaporan.show', $pelaporan->ulid) }}">Lihat</a>
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
        $('#samsat-table').DataTable({
            "language": {
                "url": '{{ asset("assets/vendors/datatables-lang-id.json") }}'
            }
        });
    </script>
@endpush
