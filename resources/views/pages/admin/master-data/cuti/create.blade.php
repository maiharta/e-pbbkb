@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tambah Data Cuti</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb"
                         class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('master-data.cuti.index') }}">Master Data
                                    Cuti</a>
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
                            <form action="{{ route('master-data.cuti.store') }}"
                                  method="POST">
                                @csrf
                                <x-input.date label="Tanggal"
                                              name="tanggal"
                                              placeholder="Masukkan tanggal cuti"
                                              value="{{ old('tanggal') }}" />
                                <x-input.text label="Deskripsi"
                                              name="deskripsi"
                                              placeholder="Masukkan keterangan cuti"
                                              value="{{ old('deskripsi') }}" />
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
        // prevent presentase_pengenaan above 100
        $('#persentase_pengenaan').on('input', function() {
            if (this.value > 100) {
                this.value = 100;
            }
        });
    </script>
@endpush
