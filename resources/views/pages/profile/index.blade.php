@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Profil Akun</h3>
                    <p class="text-subtitle text-muted">Lengkapilah profil anda untuk memulai Pelaporan</p>
                </div>
            </div>
        </div>
        <section class="section">
            @if ($user_detail)
                @if ($is_user_readonly)
                    <h5 class="p-3 text-white w-100 bg-success mb-3">Profil sedang diverifikasi oleh admin</h5>
                @endif
                @if ($user_detail->catatan_revisi)
                    <h5 class="p-3 rounded text-white w-100 bg-danger mb-3">Revisi: {{ $user_detail->catatan_revisi }}</h5>
                @endif
            @endif
            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center">
                                <img alt="..."
                                     class="rounded-circle"
                                     src="{{ auth()->user()->photo_profile }}"
                                     width="150px">
                                <h5 class="mt-4 mb-2">{{ auth()->user()->email }}</h5>
                                <p class="text-muted">{{ auth()->user()->role }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('profile.store') }}"
                                  enctype="multipart/form-data"
                                  method="POST">
                                @csrf
                                <div class="form-group mb-3">
                                    <label class="col-form-label fw-bold"
                                           for="name">Nama Perusahaan</label>
                                    <input {{ $is_user_readonly ? 'disabled' : '' }}
                                           class="form-control"
                                           id="name"
                                           name="name"
                                           placeholder="Masukkan nama perusahaan"
                                           required
                                           type="text"
                                           value="{{ old('name', auth()->user()->name) }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="col-form-label fw-bold"
                                           for="npwpd">NPWPD</label>
                                    <input {{ $is_user_readonly ? 'disabled' : '' }}
                                           class="form-control"
                                           id="npwpd"
                                           name="npwpd"
                                           placeholder="Masukkan nomor NPWPD"
                                           required
                                           type="text"
                                           value="{{ old('npwpd', auth()->user()->userDetail?->npwpd) }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label emailclass="col-form-label fw-bold"
                                           for="email">Email</label>
                                    <input {{ $is_user_readonly ? 'disabled' : '' }}
                                           class="form-control"
                                           disabled
                                           id="email"
                                           placeholder="Masukkan email"
                                           required
                                           type="text"
                                           value="{{ auth()->user()->email }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="col-form-label fw-bold"
                                           for="nomor_telepon">Nomor telepon</label>
                                    <input {{ $is_user_readonly ? 'disabled' : '' }}
                                           class="form-control"
                                           id="nomor_telepon"
                                           name="nomor_telepon"
                                           placeholder="Masukkan nomor telepon"
                                           required
                                           type="text"
                                           value="{{ old('nomor_telepon', auth()->user()->userDetail?->nomor_telepon) }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label class="col-form-label fw-bold"
                                           for="kabupaten">Kabupaten/Kota</label>
                                    <select {{ $is_user_readonly ? 'disabled' : '' }}
                                            class="form-select"
                                            id="kabupaten"
                                            name="kabupaten_id"
                                            required>
                                        <option value=""></option>
                                        @foreach ($kabupaten as $kabupaten)
                                            <option value="{{ $kabupaten->id }}">{{ $kabupaten->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="col-form-label fw-bold"
                                           for="alamat">Alamat</label>
                                    <input {{ $is_user_readonly ? 'disabled' : '' }}
                                           class="form-control"
                                           id="alamat"
                                           name="alamat"
                                           placeholder="Masukkan alamat"
                                           required
                                           type="text"
                                           value="{{ old('alamat', auth()->user()->userDetail?->alamat) }}">
                                </div>
                                <div class="form-group mb-3">
                                    <div class="mb-3">
                                        <label class="form-label"
                                               for="berkas">Berkas</label>
                                        @if ($user_detail)
                                            <a class="d-block text-decoration-underline mb-2"
                                               href="{{ route('download-file', ['uid' => auth()->user()->ulid, 'type' => 'profile_syarat']) }}"
                                               target="_blank">Lihat berkas sebelumnya</a>
                                        @endif
                                        @if (!$is_user_readonly)
                                            <input {{ $is_user_readonly ? 'disabled' : '' }}
                                                   class="form-control"
                                                   id="berkas"
                                                   name="berkas"
                                                   required
                                                   type="file">
                                        @endif
                                    </div>
                                </div>
                                @if (!$is_user_readonly)
                                    <button class="btn btn-primary w-100 d-block"
                                            type="sumbit">Simpan</button>
                                @endif
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
        $(document).ready(function() {
            $('#kabupaten').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Kabupaten',
            });

            @if (old('kabupaten_id', auth()->user()->userDetail?->kabupaten_id))
                $('#kabupaten').val({{ old('kabupaten_id', auth()->user()->userDetail?->kabupaten_id) }}).trigger(
                    'change');
            @endif
        });
    </script>
@endpush
