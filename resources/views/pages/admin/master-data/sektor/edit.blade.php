@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Data Sektor</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb"
                         class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('master-data.sektor.index') }}">Master Data
                                    Sektor</a>
                            </li>
                            <li aria-current="page"
                                class="breadcrumb-item active">Edit Data</li>
                            <li aria-current="page"
                                class="breadcrumb-item active">{{ $sektor->nama }}</li>
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
                            <form action="{{ route('master-data.sektor.update', $sektor->ulid) }}"
                                  method="POST">
                                @method('PUT')
                                @csrf
                                <div class="form-group mb-3">
                                    <label class="col-form-label fw-bold"
                                           for="kode">Kode</label>
                                    <input class="form-control"
                                           id="kode"
                                           name="kode"
                                           placeholder="Masukkan kode sektor"
                                           type="text"
                                           value="{{ old('kode', $sektor->kode) }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="col-form-label fw-bold"
                                           for="nama">Nama</label>
                                    <input class="form-control"
                                           id="nama"
                                           name="nama"
                                           placeholder="Masukkan nama sektor"
                                           type="text"
                                           value="{{ old('nama', $sektor->nama) }}">
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
                                               placeholder="Masukkan persentase tarif sektor"
                                               step="0.01"
                                               type="number"
                                               value="{{ old('persentase_tarif', $sektor->persentase_tarif) }}">
                                        <span class="input-group-text"
                                              id="basic-addon2">%</span>
                                    </div>
                                </div>
                                <div class="form-group mb-3 mt-4">
                                    <button class="btn btn-primary w-100 d-block"
                                            type="submit">Edit</button>
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
    </script>
@endpush
