@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12">
                    <h3>Laporan Penginputan</h3>
                </div>
            </div>
        </div>
        <section class="section">
            {{-- Filter Card --}}
            <div class="card filter-card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Filter Data</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger" id="validationAlert" style="display: none;">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <span id="validationMessage"></span>
                    </div>

                    <form action="#" id="filterForm" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold" for="user_id">User</label>
                                    <select class="form-select" id="user_id" name="user_id">
                                        <option value="" {{ request('user_id') == '' ? 'selected' : '' }}>Semua User</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold" for="status">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="" {{ request('status', '') == '' ? 'selected' : '' }}>Semua Status</option>
                                        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                                        <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Berjalan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <input id="periode_awal" name="periode_awal" type="hidden" value="{{ request('periode_awal') }}">
                                    <label class="form-label fw-bold" for="periode_awal_picker">Periode Awal</label>
                                    <input class="form-control" id="periode_awal_picker" name="periode_awal_picker" placeholder="Pilih periode awal" type="text">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <input id="periode_akhir" name="periode_akhir" type="hidden" value="{{ request('periode_akhir') }}">
                                    <label class="form-label fw-bold" for="periode_akhir_picker">Periode Akhir</label>
                                    <input class="form-control" id="periode_akhir_picker" name="periode_akhir_picker" placeholder="Pilih periode akhir" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="gap-2 d-flex">
                            <button class="w-100 d-block btn btn-secondary" id="resetFilter" type="button">
                                <i class="bi bi-x-circle me-1"></i> Reset Filter
                            </button>
                            <button class="w-100 d-block btn btn-primary" id="applyFilter" type="button">
                                <i class="bi bi-funnel me-1"></i> Terapkan Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Data Table Card --}}
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover"
                               id="penginputan-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Perusahaan</th>
                                    <th>Periode Laporan</th>
                                    <th>Batas Pelaporan</th>
                                    <th>Batas Pembayaran</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pelaporans as $pelaporan)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $pelaporan->user->name }}</td>
                                        <td>{{ $pelaporan->bulan_name }} {{ $pelaporan->tahun }}</td>
                                        <td>{{ $pelaporan->batas_pelaporan?->format('d-m-Y') }}</td>
                                        <td>{{ $pelaporan->batas_pembayaran?->format('d-m-Y') }}</td>
                                        <td>
                                            {!! $pelaporan->status_badge !!}
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button aria-expanded="false"
                                                        class="btn"
                                                        data-bs-toggle="dropdown"
                                                        id="dropdownMenuButton{{ $loop->iteration }}"
                                                        type="button">
                                                    <i class="isax isax-more"></i>
                                                </button>
                                                <ul aria-labelledby="dropdownMenuButton{{ $loop->iteration }}"
                                                    class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item"
                                                           href="{{ route('laporan.penginputan.sptpd.show', $pelaporan->ulid) }}">
                                                            <i class="isax isax-document-text"></i> Lihat SPTPD
                                                        </a>
                                                    </li>
                                                    @if($pelaporan->is_sptpd_approved)
                                                        <li>
                                                            <a class="dropdown-item"
                                                               href="{{ route('laporan.penginputan.sptpd.download', $pelaporan->ulid) }}">
                                                                <i class="isax isax-document-download"></i> Download SPTPD
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item"
                                                               href="{{ route('laporan.penginputan.sspd.show', $pelaporan->ulid) }}">
                                                                <i class="isax isax-document-text"></i> Lihat SSPD
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item"
                                                               href="{{ route('laporan.penginputan.sspd.download', $pelaporan->ulid) }}">
                                                                <i class="isax isax-document-download"></i> Download SSPD
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <li>
                                                        <a class="dropdown-item"
                                                           href="{{ route('laporan.penginputan.penjualan.show', $pelaporan->ulid) }}">
                                                            <i class="isax isax-chart"></i> Lihat Penjualan
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                           href="{{ route('laporan.penginputan.pembelian.show', $pelaporan->ulid) }}">
                                                            <i class="isax isax-shopping-cart"></i> Lihat Pembelian
                                                        </a>
                                                    </li>
                                                    @if($pelaporan->is_sptpd_approved)
                                                        <li>
                                                            <a class="dropdown-item"
                                                               href="{{ route('laporan.penginputan.invoices.show', $pelaporan->ulid) }}">
                                                                <i class="isax isax-receipt-1"></i> Lihat Invoice
                                                            </a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#penginputan-table').DataTable({
                "language": {
                    "url": '{{ asset("assets/vendors/datatables-lang-id.json") }}'
                }
            });

            // Initialize Flatpickr for date pickers
            let startPicker = flatpickr("#periode_awal_picker", {
            plugins: [
                new monthSelectPlugin({
                    shorthand: true,
                    dateFormat: "F Y",
                    altFormat: "F Y",
                    theme: "light",
                })
            ],
            onValueUpdate: function(selectedDates, dateStr, instance) {
                let month = instance.currentMonth + 1;
                let year = instance.currentYear;
                let formattedDate = month + '-' + year;
                $('#periode_awal').val(formattedDate);
                validateDates();
            }
        });

        let endPicker = flatpickr("#periode_akhir_picker", {
            plugins: [
                new monthSelectPlugin({
                    shorthand: true,
                    dateFormat: "F Y",
                    altFormat: "F Y",
                    theme: "light",
                })
            ],
            onValueUpdate: function(selectedDates, dateStr, instance) {
                let month = instance.currentMonth + 1;
                let year = instance.currentYear;
                let formattedDate = month + '-' + year;
                $('#periode_akhir').val(formattedDate);
                validateDates();
            }
        });

        // Set default values if present
        @if(request('periode_awal'))
            let periodeAwal = '{{ request("periode_awal") }}'.split('-');
            let bulanAwal = parseInt(periodeAwal[0]) - 1;
            let tahunAwal = parseInt(periodeAwal[1]);
            startPicker.setDate(new Date(tahunAwal, bulanAwal));
        @endif

        @if(request('periode_akhir'))
            let periodeAkhir = '{{ request("periode_akhir") }}'.split('-');
            let bulanAkhir = parseInt(periodeAkhir[0]) - 1;
            let tahunAkhir = parseInt(periodeAkhir[1]);
            endPicker.setDate(new Date(tahunAkhir, bulanAkhir));
        @endif

        // Date validation function
        function validateDates() {
            const startDate = startPicker.selectedDates[0];
            const endDate = endPicker.selectedDates[0];

            if (startDate && endDate) {
                if (startDate > endDate) {
                    showValidationError("Periode awal tidak boleh lebih besar dari periode akhir.");
                    return false;
                }
            }

            hideValidationError();
            return true;
        }

        function showValidationError(message) {
            $('#validationMessage').text(message);
            $('#validationAlert').show();
        }

        function hideValidationError() {
            $('#validationAlert').hide();
        }

        // Reset filter
        $('#resetFilter').on('click', function() {
            $('#filterForm')[0].reset();
            startPicker.clear();
            endPicker.clear();
            $('#periode_awal').val('');
            $('#periode_akhir').val('');
            hideValidationError();

            // Reload page without filters
            window.location.href = '{{ route("laporan.penginputan.index") }}';
        });

        // Apply filter
        $('#applyFilter').on('click', function() {
            const periodeAwal = $('#periode_awal').val();
            const periodeAkhir = $('#periode_akhir').val();

            // Validate if both dates are filled or both are empty
            if ((periodeAwal && !periodeAkhir) || (!periodeAwal && periodeAkhir)) {
                showValidationError('Harap isi kedua periode atau kosongkan keduanya.');
                return;
            }

            // Validate date range if both filled
            if (periodeAwal && periodeAkhir) {
                if (!validateDates()) {
                    return;
                }
            }

            // Submit form
            const userId = $('#user_id').val();
            const status = $('#status').val();
            let url = '{{ route("laporan.penginputan.index") }}?';

            let params = [];
            if (userId) params.push('user_id=' + userId);
            if (status) params.push('status=' + status);
            if (periodeAwal && periodeAkhir) {
                params.push('periode_awal=' + periodeAwal);
                params.push('periode_akhir=' + periodeAkhir);
            }

            url += params.join('&');
            window.location.href = url;
        });
    });
    </script>
@endpush
