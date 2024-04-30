@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12">
                    <h3>Pelaporan</h3>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover"
                               id="pelaporan-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th class="text-start">Bulan Pajak</th>
                                    <th class="text-start">Tahun Pajak</th>
                                    <th class="text-start">Pembelian</th>
                                    <th class="text-start">Penjualan</th>
                                    <th class="text-start">SPTPD</th>
                                    <th class="text-start">SSPD</th>
                                    <th class="text-start">Kirim</th>
                                    <th class="text-start">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pelaporans as $pelaporan)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-start">{{ $pelaporan->bulan_name }}</td>
                                        <td class="text-start">{{ $pelaporan->tahun }}</td>
                                        <td class="text-center">{!! $pelaporan->pembelian_badge !!}</td>
                                        <td class="text-center">{!! $pelaporan->penjualan_badge !!}</td>
                                        <td class="text-center">{!! $pelaporan->sptpd_badge !!}</td>
                                        <td class="text-center">{!! $pelaporan->sspd_badge !!}</td>
                                        <td class="text-center">{!! $pelaporan->send_badge !!}</td>
                                        <td class="text-center">{!! $pelaporan->status_badge !!}</td>
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
        $('#pelaporan-table').DataTable({
            "responsive": true,
            "language": {
                "url": '{{ asset('assets/vendors/datatables-lang-id.json') }}'
            }
        });
    </script>
@endpush
