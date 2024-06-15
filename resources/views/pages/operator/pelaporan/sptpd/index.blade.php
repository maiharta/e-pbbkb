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
                    <table>
                        <tr>
                            <td class="pb-1 pe-4">Nama perusahaan</td>
                            <th>: {{ $pelaporan->user->name }}</th>
                        </tr>
                        <tr>
                            <td class="pb-1 pe-4">Periode Laporan</td>
                            <th>: {{ $pelaporan->bulan_name }} {{ $pelaporan->tahun }}</th>
                        </tr>
                        <tr>
                            <td class="pb-1 pe-4">Tanggal Approval SPTPD</td>
                            <th>:
                                {{ Carbon\Carbon::parse($pelaporan->sptpd_approved_at)->locale('id')->isoFormat('LL') }}
                            </th>
                        </tr>
                        <tr>
                            <td class="pb-1 pe-4">Nomor SPTPD</td>
                            <th>: {{ $pelaporan->sptpd->nomor }}</th>
                        </tr>
                        <tr>
                            <td class="pb-1 pe-4">NPWPD</td>
                            <th>: {{ $pelaporan->user->userDetail->npwpd }}</th>
                        </tr>
                    </table>
                </div>
            </div>
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
                                                <tr class="table-secondary-custom">
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
                                            <tr class="table-secondary-custom">
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
                                            <tr class="table-secondary-custom">
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
                                        <tr class="table-secondary-custom">
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
                    @if (!$pelaporan->is_sptpd_approved)
                        <div class="d-flex gap-2 mt-3">
                            <button class="btn btn-secondary w-100"
                                    onclick="cancelSptpd('{{ $pelaporan->ulid }}')"><span
                                      class="isax isax-back-square"></span>
                                Perbaiki Data</button>
                            <button class="btn btn-primary w-100"
                                    data-bs-target="#staticBackdrop"
                                    data-bs-toggle="modal"><span class="isax isax-add-square"></span> Surat
                                Pernyataan</button>
                        </div>
                    @endif
                </div>
            </div>
            <div aria-hidden="true"
                 aria-labelledby="staticBackdropLabel"
                 class="modal fade"
                 data-bs-backdrop="static"
                 data-bs-keyboard="false"
                 id="staticBackdrop"
                 tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"
                                id="staticBackdropLabel">Pernyataan</h5>
                            <button aria-label="Close"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    type="button"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group mb-3">
                                        <label class="col-form-label fw-bold"
                                               for="periode">Periode</label>
                                        <input class="form-control"
                                               disabled
                                               id="periode"
                                               name="periode"
                                               type="text"
                                               value="{{ $pelaporan->bulan_name }} - {{ $pelaporan->tahun }}">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group mb-3">
                                        <label class="col-form-label fw-bold"
                                               for="pbbkb">Jumlah pemungutan PBBKB</label>
                                        <input class="form-control"
                                               disabled
                                               id="pbbkb"
                                               name="pbbkb"
                                               type="text"
                                               value="Rp {{ number_format($pelaporan->data_formatted->values()->map(fn($item) => $item->values()->pluck('subtotal')->sum('pbbkb'))->sum(), 2, ',', '.') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="col-form-label fw-bold"
                                       for="periode">Wajib Pungut</label>
                                <input class="form-control"
                                       disabled
                                       id="periode"
                                       name="periode"
                                       type="text"
                                       value="{{ auth()->user()->name }}">
                            </div>

                            <div class="form-group mb-3">
                                <label class="col-form-label fw-bold"
                                       for="pbbkb">NPWPD</label>
                                <input class="form-control"
                                       disabled
                                       id="pbbkb"
                                       name="pbbkb"
                                       type="text"
                                       value="{{ auth()->user()->userDetail->npwpd }}">
                            </div>
                            <div class="form-group mb-3">
                                <label class="col-form-label fw-bold"
                                       for="nomor_sptpd">Nomor SPTPD*</label>
                                <input class="form-control"
                                       disabled
                                       id="nomor_sptpd"
                                       placeholder="Masukkan nomor SPTPD perusahaan"
                                       type="text"
                                       value="{{ $pelaporan->sptpd->nomor }}">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary"
                                    data-bs-dismiss="modal"
                                    type="button">Batal</button>
                            <button class="btn btn-primary"
                                    onclick="approveSptpd('{{ $pelaporan->ulid }}')"
                                    type="button">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        function cancelSptpd(ulid) {
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

        function approveSptpd(ulid) {
            const nomor_sptpd = $('#nomor_sptpd').val();
            if (nomor_sptpd == null || nomor_sptpd == '') {
                Toast.fire({
                    'icon': 'error',
                    'text': 'Semua kolom dengan tanda (*) wajib diisi'
                });
                return;
            }
            Swal.fire({
                'title': 'Apakah anda yakin?',
                'showCancelButton': true,
                'confirmButtonText': 'Ya, Simpan',
                'cancelButtonText': 'Batal',
                'reverseButtons': true,
                'customClass': {
                    confirmButton: 'btn btn-primary ms-2',
                    cancelButton: 'btn btn-outline-secondary'
                },
                'html': '<p class="text-start text-sm fw-bold">Silahkan konfirmasi data di bawah ini</p>' +
                    '<table class="table text-sm table-bordered">' +
                    '<tr>' +
                    '<td>Wajib Pungut</td>' +
                    '<th> {{ auth()->user()->name }}</th>' +
                    '</tr>' +
                    '<tr>' +
                    '<td>NPWPD</td>' +
                    '<th> {{ auth()->user()->userDetail->npwpd }}</th>' +
                    '</tr>' +
                    '<tr>' +
                    '<td>Periode</td>' +
                    '<th> {{ $pelaporan->bulan_name }} - {{ $pelaporan->tahun }}</th>' +
                    '</tr>' +
                    '<tr>' +
                    '<td>Jumlah Pemungutan PBBKB</td>' +
                    '<th> Rp {{ number_format($pelaporan->data_formatted->values()->map(fn($item) => $item->values()->pluck('subtotal')->sum('pbbkb'))->sum(), 2, ',', '.') }}</th>' +
                    '</tr>' +
                    '<tr>' +
                    '<td>Nomor SPTPD</td>' +
                    '<th> ' + nomor_sptpd + '</th>' +
                    '</tr>' +
                    '</table>' +
                    '<p class="text-danger fw-bold text-sm mb-0">*Dengan menyadari sepenuhnya akan segala akibat termasuk sanksi sesuai dengan ketentuan perundang-undangan yang berlaku, saya atau yang saya beri kuasa menyatakan bahwa apa yang telah kami beritahu tersebut beserta lampiran-lampirannya adalah benar, lengkap, dan jelas</p>',
            }).then((result) => {
                if (result.isConfirmed) {
                    // ajax
                    $.ajax({
                        'url': '{{ route('pelaporan.sptpd.approve') }}/' + ulid,
                        'type': 'POST',
                        'data': {
                            '_token': '{{ csrf_token() }}',
                            'nomor_sptpd': nomor_sptpd
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
