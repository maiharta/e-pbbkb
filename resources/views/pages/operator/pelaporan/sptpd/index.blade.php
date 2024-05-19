@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>SSTPD</h3>
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
            <div class="card">
                <div class="card-body">
                    @foreach ($pelaporan->penjualan->groupBy('nama_sektor') as $nama_sektor => $pelaporan_1)
                    {{-- {{ $loop->iteration == 2 ? $pelaporan_1->dd() :'' }} --}}
                        <p class="mt-3 mb-2 fw-bold">{{ $loop->iteration }}. {{ $nama_sektor }}</p>
                        <div class="ms-4">
                        @foreach ($pelaporan_1->groupBy('is_subsidi') as $is_subsidi => $pelaporan_2)
                            <p class="mb-0">{{ chr(64 + $loop->iteration) }}. {{ $is_subsidi ? 'Subsidi' : 'Umum' }}</p>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>BBM</th>
                                        <th>Tarif</th>
                                        <th>Volume (Liter)</th>
                                        <th>DPP</th>
                                        <th>PBBKB</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- {{ $loop->iteration == 2 ?$pelaporan_2->groupBy('nama_jenis_bbm')->dd():''  }} --}}
                                    @foreach ($pelaporan_2->groupBy('jenis_bbm_id') as $key => $value)
                                    <tr>
                                        <td width="16.67%">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td width="16.67%">
                                            {{ $value->first()->nama_jenis_bbm }}
                                        </td>
                                        <td width="16.67%">
                                            {{ $value->first()->persentase_tarif_jenis_bbm + $value->first()->persentase_tarif_sektor }}%
                                        </td>
                                        <td width="16.67%">
                                            {{ number_format($value->sum('volume'), 0, ',', '.') }}
                                        </td>
                                        <td width="16.67%">
                                             Rp. {{ number_format($value->sum('dpp'), 0, ',', '.') }}
                                        </td>
                                        <td width="16.67%">
                                            Rp. {{ number_format($value->sum('dpp')*($value->first()->persentase_tarif_jenis_bbm + $value->first()->persentase_tarif_sektor)/100, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="3" class="fw-bold">SUBTOTAL</td>
                                        <td class="fw-bold">{{ number_format($pelaporan_2->sum('volume'), 0, ',', '.') }}</td>
                                        <td class="fw-bold">Rp. {{ number_format($pelaporan_2->sum('dpp'), 0, ',', '.') }}</td>
                                        <td class="fw-bold">Rp. {{ number_format($pelaporan_2->groupBy('jenis_bbm_id')->map(fn($item)=>$item->first())->map(fn($item)=>$value->sum('dpp')*($value->first()->persentase_tarif_jenis_bbm + $value->first()->persentase_tarif_sektor)/100)->sum(), 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
@endsection
