@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12">
                    <h3>Master Data Sektor</h3>
                </div>
            </div>
        </div>
        <section class="section">
            <a class="btn btn-primary mb-3"
               href="{{ route('master-data.sektor.create') }}">+ Tambah Data</a>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover"
                               id="sektor-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th class="text-start">Kode</th>
                                    <th class="text-start">Nama</th>
                                    <th class="text-start">Tarif</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sektors as $sektor)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-start">{{ $sektor->kode }}</td>
                                        <td class="text-start">{{ $sektor->nama }}</td>
                                        <td class="text-start">{{ $sektor->persentase_tarif }} %</td>
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
                                                           href="{{ route('master-data.sektor.edit', $sektor->ulid) }}">Edit</a>
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
        $('#sektor-table').DataTable({
            "responsive": true,
            "language": {
                "url": '{{ asset('assets/vendors/datatables-lang-id.json') }}'
            }
        });
    </script>
@endpush
