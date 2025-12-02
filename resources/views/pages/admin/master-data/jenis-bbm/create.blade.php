@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tambah Data Jenis BBM</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb"
                         class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('master-data.jenis-bbm.index') }}">Master Data
                                    Jenis BBM</a>
                            </li>
                            <li aria-current="page"
                                class="breadcrumb-item active">Tambah Data</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('master-data.jenis-bbm.store') }}"
                                  method="POST">
                                @csrf
                                <div class="form-group mb-3">
                                    <label class="col-form-label fw-bold"
                                           for="kode">Kode</label>
                                    <input class="form-control"
                                           id="kode"
                                           name="kode"
                                           placeholder="Masukkan kode jenis BBM"
                                           type="text"
                                           value="{{ old('kode') }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="col-form-label fw-bold"
                                           for="nama">Nama</label>
                                    <input class="form-control"
                                           id="nama"
                                           name="nama"
                                           placeholder="Masukkan nama jenis BBM"
                                           type="text"
                                           value="{{ old('nama') }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="col-form-label fw-bold"
                                           for="is_subsidi">Tipe Subsidi</label>
                                    <select name="is_subsidi" id="is_subsidi" class="form-select" required>
                                        <option></option>
                                        <option value="1">Subsidi</option>
                                        <option value="0">Non Subsidi</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="col-form-label fw-bold"
                                           for="persentase_tarif">Tarif</label>
                                    <div class="input-group">
                                        <input aria-describedby="basic-addon2"
                                               class="form-control"
                                               id="persentase_tarif"
                                               max="100"
                                               min="0"
                                               name="persentase_tarif"
                                               placeholder="Masukkan persentase tarif jenis BBM"
                                               step="0.01"
                                               type="number"
                                               value="{{ old('persentase_tarif') }}">
                                        <span class="input-group-text"
                                              id="basic-addon2">%</span>
                                    </div>
                                </div>
                                <div class="form-group mb-3 mt-4">
                                    <button class="btn btn-primary w-100 d-block"
                                            type="submit">Tambah</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        // prevent presentase_tarif above 100
        $('#persentase_tarif').on('input', function() {
            if (this.value > 100) {
                this.value = 100;
            }
        });

        $('#is_subsidi').select2({
            theme: 'bootstrap-5',
            placeholder: 'Pilih tipe subsidi',
            allowClear: true
        });

        @if (old('is_subsidi'))
            $('#is_subsidi').val('{{ old('is_subsidi') }}').trigger('change');
        @endif
    </script>
@endpush
