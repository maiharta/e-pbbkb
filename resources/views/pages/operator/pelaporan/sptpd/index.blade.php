@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>SPTPD</h3>
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
                                <p class="mb-0 fw-bold">{{ chr(64 + $loop->iteration) }}. {{ $category }}
                                </p>
                                <div class="table-responsive">
                                    <table class="table">
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
                                                <tr class="table-secondary">
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
                                                        Rp {{ number_format($item->get('dpp'), 2, ',', '.') }}
                                                    </td>
                                                    <td width="16.67%">
                                                        Rp
                                                        {{ number_format($item->get('pbbkb'), 2, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr class="table-secondary">
                                                <td class="fw-bold"
                                                    colspan="3">SUBTOTAL</td>
                                                <td class="fw-bold">
                                                    {{ number_format($items['subtotal']->get('volume'), 0, ',', '.') }}
                                                </td>
                                                <td class="fw-bold">Rp
                                                    {{ number_format($items['subtotal']->get('dpp'), 2, ',', '.') }}</td>
                                                <td class="fw-bold">Rp
                                                    {{ number_format($items['subtotal']->get('pbbkb'), 2, ',', '.') }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                        </div>
                        @if ($loop->last)
                            <p class="mt-3 mb-2 fw-bold">{{ $loop->iteration + 1 }}. Total</p>
                            <div class="table-responsive ms-4">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Sektor</th>
                                            <th>Volume (Liter)</th>
                                            <th>DPP</th>
                                            <th>PBBKB</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pelaporan->data_formatted as $sektor => $categories)
                                            <tr class="table-secondary">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $sektor }}</td>
                                                <td>{{ number_format($categories->values()->pluck('subtotal')->sum('volume'), 0, ',', '.') }}
                                                </td>
                                                <td>Rp
                                                    {{ number_format($categories->values()->pluck('subtotal')->sum('dpp'), 2, ',', '.') }}
                                                </td>
                                                <td>Rp
                                                    {{ number_format($categories->values()->pluck('subtotal')->sum('pbbkb'), 2, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-secondary">
                                            <td class="fw-bold"
                                                colspan="2">Total</td>
                                            <td class="fw-bold">
                                                {{ number_format($pelaporan->data_formatted->values()->map(fn($item) => $item->values()->pluck('subtotal')->sum('volume'))->sum(), 0, ',', '.') }}
                                            </td>
                                            <td class="fw-bold">Rp
                                                {{ number_format($pelaporan->data_formatted->values()->map(fn($item) => $item->values()->pluck('subtotal')->sum('dpp'))->sum(), 2, ',', '.') }}
                                            </td>
                                            <td class="fw-bold">Rp
                                                {{ number_format($pelaporan->data_formatted->values()->map(fn($item) => $item->values()->pluck('subtotal')->sum('pbbkb'))->sum(), 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endforeach
                    <div class="d-flex gap-2 mt-3">
                        <button onclick="cancelSptpd('{{ $pelaporan->ulid }}')" class="btn btn-secondary w-100"><span class="isax isax-back-square"></span> Perbaiki Data</button>
                        <button class="btn btn-primary w-100 alinve"><span class="isax isax-add-square"></span> Surat Pernyataan</button>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        function cancelSptpd(ulid){
            Swal.fire({
                'title': 'Apakah anda yakin?',
                'text': 'Anda akan membatalkan proses SPTPD dan memperbaiki data pelaporan kemudian melakukan verifikasi ulang ke admin',
                'icon': 'warning',
                'showCancelButton': true,
                'confirmButtonText': 'Ya',
                'cancelButtonText': 'Batal',
                'customClass': {
                    confirmButton: 'btn btn-secondary me-2',
                    cancelButton: 'btn btn-outline-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // ajax
                    $.ajax({
                        'url': '{{ route('pelaporan.sptpd.cancel') }}/' + ulid,
                        'type': 'POST',
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
                                    window.location.href = '{{ route('pelaporan.index') }}'
                                });
                            }
                        },
                        'error': function(data) {
                            Swal.fire({
                                'title': 'Gagal',
                                'text': data.message,
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
