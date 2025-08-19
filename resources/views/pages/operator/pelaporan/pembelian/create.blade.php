@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tambah Data Pembelian</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb"
                         class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('pelaporan.index') }}">Pelaporan</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                   href="{{ route('pelaporan.pembelian.index', $pelaporan->ulid) }}">Data
                                    Pembelian {{ $pelaporan->bulan_name }}
                                    {{ $pelaporan->tahun }}</a>
                            </li>
                            <li aria-current="page"
                                class="breadcrumb-item active">Tambah</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('pelaporan.pembelian.store', $pelaporan->ulid) }}"
                          method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="col-form-label fw-bold"
                                   for="penjual">Penjual</label>
                            <input class="form-control"
                                   id="penjual"
                                   name="penjual"
                                   placeholder="Masukkan nama penjual"
                                   type="text"
                                   value="{{ old('penjual') }}">
                        </div>
                        <div class="form-group mb-3">
                            <label class="col-form-label fw-bold"
                                   for="alamat">Alamat</label>
                            <input class="form-control"
                                   id="alamat"
                                   name="alamat"
                                   placeholder="Masukkan nama alamat"
                                   type="text"
                                   value="{{ old('alamat') }}">
                        </div>
                        <div class="form-group mb-3">
                            <label class="col-form-label fw-bold"
                                   for="jenis_bbm">Jenis BBM</label>
                            <select class="form-select"
                                    id="jenis_bbm"
                                    name="jenis_bbm_id">
                                <option value=""></option>
                                @foreach ($jenis_bbms as $jenis_bbm)
                                    <option value="{{ $jenis_bbm->id }}"><span
                                              class="fw-bold">{{ $jenis_bbm->is_subsidi ? 'Subsidi' : 'Non Subsidi' }}</span>
                                        - {{ $jenis_bbm->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="col-form-label fw-bold"
                                   for="sisa_volume">Sisa Volume BBM (Liter)</label>
                            <input autocomplete="off"
                                   class="form-control"
                                   id="sisa_volume"
                                   name="sisa_volume"
                                   placeholder="Masukkan sisa  volume"
                                   type="text"
                                   value="{{ old('sisa_volume') }}">
                        </div>
                        <div class="form-group mb-3">
                            <label class="col-form-label fw-bold"
                                   for="volume">Total Volume Pembelian (Liter)</label>
                            <input autocomplete="off"
                                   class="form-control"
                                   id="volume"
                                   name="volume"
                                   placeholder="Masukkan total volume"
                                   type="text"
                                   value="{{ old('volume') }}">
                        </div>
                        <div class="form-group mb-3">
                            <label class="col-form-label fw-bold"
                                   for="nomor_kuitansi">Nomor Kuitansi Pembelian</label>
                            <input class="form-control"
                                   id="nomor_kuitansi"
                                   name="nomor_kuitansi"
                                   placeholder="Masukkan nama nomor kuitansi pembelian"
                                   type="text"
                                   value="{{ old('nomor_kuitansi') }}">
                        </div>
                        {{-- <div class="form-group mb-3">
                            <label class="col-form-label fw-bold"
                                   for="tanggal">Tanggal Pembelian</label>
                            <input class="form-control"
                                   id="tanggal"
                                   name="tanggal"
                                   placeholder="Pilih Tanggal Referensi Pembayaran"
                                   required
                                   type="text">
                        </div> --}}
                        <x-input.date label="Tanggal Penjualan"
                                      name="tanggal"
                                      placeholder="Masukkan tanggal penjualan"
                                      settings="minDate: moment().set('month', {{ $pelaporan->bulan }} - 1).startOf('month').format('YYYY-MM-DD'),maxDate: moment().set('month', {{ $pelaporan->bulan }} - 1).endOf('month').format('YYYY-MM-DD'),"
                                      value="{{ old('tanggal') }}" />
                        <button class="btn btn-primary d-block w-100">Tambah</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        $('#jenis_bbm').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih Jenis BBM',
            allowClear: true
        });

        new AutoNumeric('#sisa_volume', {
            decimalCharacter: ',',
            digitGroupSeparator: '.',
            decimalPlaces: 0,
            unformatOnSubmit: true,
        });

        new AutoNumeric('#volume', {
            decimalCharacter: ',',
            digitGroupSeparator: '.',
            decimalPlaces: 0,
            unformatOnSubmit: true,
        });

        @if (old('jenis_bbm_id'))
            $('#jenis_bbm').val({{ old('jenis_bbm_id') }}).trigger('change');
        @endif
    </script>
@endpush
