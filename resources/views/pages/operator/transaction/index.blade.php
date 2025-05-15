@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Data Transaksi</h3>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <table class="table"
                           id="table">
                        <thead>
                            <tr>
                                <th class="text-start">No</th>
                                <th>Periode</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pelaporans as $pelaporan)
                                <tr>
                                    <td class="text-start">{{ $loop->iteration }}</td>
                                    <td>{{ $pelaporan->bulan_name }} - {{ $pelaporan->tahun }}</td>
                                    <td class="text-center">{!! $pelaporan->status_badge !!}</td>
                                    <td class="text-center">
                                        {{-- dropdown with isax three dot vertical icon --}}
                                        <div class="dropdown">
                                            <button aria-expanded="false"
                                                    class="btn"
                                                    data-bs-toggle="dropdown"
                                                    id="dropdownMenuButton"
                                                    type="button">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul aria-labelledby="dropdownMenuButton"
                                                class="dropdown-menu">
                                                <li><a class="dropdown-item"
                                                       href="{{ route('invoices.show', $pelaporan->ulid) }}">
                                                        <i class="bi bi-file-earmark-text"></i> List Invoice</a>
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
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#table').DataTable();
        });
    </script>
@endpush
