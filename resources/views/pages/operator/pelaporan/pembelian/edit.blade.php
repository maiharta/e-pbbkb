@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Data Pembelian</h3>
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
                                class="breadcrumb-item active">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('pelaporan.pembelian.update', ['pembelian' => $pembelian->ulid, 'ulid' => $pelaporan->ulid]) }}"
                          method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-3">
                            <label class="col-form-label fw-bold"
                                   for="penjual">Penjual</label>
                            <input class="form-control"
                                   id="penjual"
                                   name="penjual"
                                   placeholder="Masukkan nama penjual"
                                   type="text"
                                   value="{{ old('penjual', $pembelian->penjual) }}">
                        </div>
                        <div class="form-group mb-3">
                            <label class="col-form-label fw-bold"
                                   for="kabupaten">Kabupaten/Kota</label>
                            <select class="form-select"
                                    id="kabupaten"
                                    name="kabupaten_id">
                                <option value=""></option>
                                @foreach ($kabupatens as $kabupaten)
                                    <option value="{{ $kabupaten->id }}">{{ $kabupaten->nama }}</option>
                                @endforeach
                            </select>
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
                                   for="volume">Total Volume (Liter)</label>
                            <input autocomplete="off"
                                   class="form-control"
                                   id="volume"
                                   name="volume"
                                   placeholder="Masukkan total volume"
                                   type="text"
                                   value="{{ old('volume', $pembelian->volume) }}">
                        </div>
                        <button class="btn btn-primary d-block w-100">Simpan</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        $('#kabupaten').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih Kabupaten/Kota',
        });

        $('#jenis_bbm').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih Jenis BBM',
        });

        new AutoNumeric('#volume', {
            decimalCharacter: ',',
            digitGroupSeparator: '.',
            decimalPlaces: 0,
            unformatOnSubmit: true,
        })

        @if (old('kabupaten_id', $pembelian->kabupaten_id))
            $('#kabupaten').val({{ old('kabupaten_id', $pembelian->kabupaten_id) }}).trigger('change');
        @endif
        @if (old('jenis_bbm_id', $pembelian->jenis_bbm_id))
            $('#jenis_bbm').val({{ old('jenis_bbm_id', $pembelian->jenis_bbm_id) }}).trigger('change');
        @endif
    </script>
@endpush
