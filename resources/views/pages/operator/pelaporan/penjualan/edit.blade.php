@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Data Penjualan</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb"
                         class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('pelaporan.index') }}">Pelaporan</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                   href="{{ route('pelaporan.penjualan.index', $pelaporan->ulid) }}">Data
                                    Penjualan {{ $pelaporan->bulan_name }}
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
                    <form action="{{ route('pelaporan.penjualan.update', ['penjualan' => $penjualan->ulid, 'ulid' => $pelaporan->ulid]) }}"
                          method="POST">
                        @csrf
                        @method('PUT')
                        <x-input.text label="Pembeli"
                                      name="pembeli"
                                      placeholder="Masukkan nama pembeli"
                                      value="{{ old('pembeli', $penjualan->pembeli) }}" />
                        <x-input.text label="Alamat"
                                      name="alamat"
                                      placeholder="Masukkan nama alamat"
                                      value="{{ old('alamat', $penjualan->alamat) }}" />
                        <x-input.select :options="$sektors->map(fn($item) => ['key' => $item->id, 'value' => $item->nama])"
                                        label="Sektor"
                                        name="sektor_id"
                                        placeholder="Pilih sektor"
                                        value="{{ old('sektor_id', $penjualan->sektor_id) }}" />
                        <x-input.select :options="$jenis_bbms->map(
                            fn($item) => [
                                'key' => $item->id,
                                'value' => $item->nama . ' - ' . ($item->is_subsidi ? 'Subsidi' : 'Non Subsidi'),
                            ],
                        )"
                                        label="Jenis BBM"
                                        name="jenis_bbm_id"
                                        placeholder="Pilih jenis BBM"
                                        value="{{ old('jenis_bbm_id', $penjualan->jenis_bbm_id) }}" />
                        <x-input.number label="Total Volume (Liter)"
                                        name="volume"
                                        placeholder="Masukkan total volume"
                                        value="{{ old('volume', $penjualan->volume) }}" />
                        <x-input.number :is_currency="true"
                                        label="Total DPP"
                                        name="dpp"
                                        placeholder="Masukkan total DPP"
                                        value="{{ old('dpp', $penjualan->dpp) }}" />
                        <x-input.text label="Nomor Kuitansi Pembelian"
                                      name="nomor_kuitansi"
                                      placeholder="Masukkan nomor kuitansi pembelian"
                                      value="{{ old('nomor_kuitansi', $penjualan->nomor_kuitansi) }}" />
                        <x-input.date label="Tanggal Penjualan"
                                      name="tanggal"
                                      placeholder="Masukkan tanggal penjualan"
                                      settings="minDate: moment().set('month', {{ $pelaporan->bulan }} - 1).startOf('month').format('YYYY-MM-DD'),maxDate: moment().set('month', {{ $pelaporan->bulan }} - 1).endOf('month').format('YYYY-MM-DD'),"
                                      value="{{ old('tanggal', $penjualan->tanggal) }}"/>
                        <x-input.number :currency="true"
                                        :is_currency="true"
                                        label="PBBKB"
                                        name="pbbkb"
                                        placeholder="Masukkan total PBBKB"
                                        value="{{ old('pbbkb', $penjualan->pbbkb) }}" />
                        <x-input.select :options="[['key' => 'depot', 'value' => 'Depot'], ['key' => 'TBBM', 'value' => 'TBBM']]"
                                        label="Lokasi Penyaluran"
                                        name="lokasi_penyaluran"
                                        placeholder="Pilih lokasi penyaluran"
                                        value="{{ old('lokasi_penyaluran',$penjualan->lokasi_penyaluran) }}" />
                        <x-input.select :options="[
                            ['key' => '1', 'value' => 'Wajib Pajak'],
                            ['key' => '0', 'value' => 'Tidak Wajib Pajak'],
                        ]"
                                        label="Status Pajak"
                                        name="is_wajib_pajak"
                                        placeholder="Pilih status pajak pembeli"
                                        value="{{ old('is_wajib_pajak',$penjualan->is_wajib_pajak) }}" />
                        <button class="btn btn-primary d-block w-100">Simpan</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
@endpush
