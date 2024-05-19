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
                    @foreach ($pelaporan->data_formatted as $sektor => $categories)
                        <p class="mt-3 mb-2 fw-bold">{{ $loop->iteration }}. {{ $sektor }}</p>
                        <div class="ms-4">
                            @foreach ($categories as $category => $items)
                                <p class="mb-0">{{ chr(64 + $loop->iteration) }}. {{ $category }}
                                </p>
                                <div class="table-responsive">
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
                                            @foreach ($items['items'] as $key => $item)
                                                <tr>
                                                    <td width="5%">
                                                        {{ $loop->iteration }}
                                                    </td>
                                                    <td width="10%">
                                                        {{ $item->get('nama_jenis_bbm') }}
                                                    </td>
                                                    <td width="10%">
                                                        {{ $item->get('persentase_tarif') }}%
                                                    </td>
                                                    <td width="16.67%">
                                                        {{ number_format($item->get('volume'), 0, ',', '.') }}
                                                    </td>
                                                    <td width="16.67%">
                                                        Rp. {{ number_format($item->get('dpp'), 2, ',', '.') }}
                                                    </td>
                                                    <td width="16.67%">
                                                        Rp.
                                                        {{ number_format($item->get('pbbkb'), 2, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td class="fw-bold"
                                                    colspan="3">SUBTOTAL</td>
                                                <td class="fw-bold">
                                                    {{ number_format($items['subtotal']->get('volume'), 0, ',', '.') }}
                                                </td>
                                                <td class="fw-bold">Rp.
                                                    {{ number_format($items['subtotal']->get('dpp'), 2, ',', '.') }}</td>
                                                <td class="fw-bold">Rp.
                                                    {{ number_format($items['subtotal']->get('pbbkb'), 2, ',', '.') }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
@endsection
