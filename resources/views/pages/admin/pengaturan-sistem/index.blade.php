@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Pengaturan Sistem</h3>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="row">
                <div class="col">
                    <form action="{{ route('pengaturan-sistem.update') }}"
                          method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card">
                            <div class="card-body">
                                <x-input.number :is_currency="false"
                                                group="Hari"
                                                label="Batas hari pelaporan"
                                                name="batas_pelaporan"
                                                placeholder="Masukkan batas hari pelaporan"
                                                value="{{ old('batas_pelaporan', $pengaturan_sistem->where('key', 'batas_pelaporan')->first()->value) }}" />
                                <x-input.number :is_currency="false"
                                                group="Hari"
                                                label="Batas hari pembayaran"
                                                name="batas_pembayaran"
                                                placeholder="Masukkan batas hari pembayaran"
                                                value="{{ old('batas_pembayaran', $pengaturan_sistem->where('key', 'batas_pembayaran')->first()->value) }}" />
                            </div>
                        </div>
                        <button class="btn btn-primary mt-3 ms-auto w-100"
                                type="submit">Simpan</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script></script>
@endpush
