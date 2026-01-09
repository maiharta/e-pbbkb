@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12">
                    <h3>Master Data User</h3>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card mb-3">
                <div class="card-body">
                    <form action="{{ route('master-data.user.index') }}" method="GET" id="filterForm">
                        <div class="row align-items-end g-2">
                            <div class="col-md-6">
                                <label class="form-label fw-bold mb-2" for="daterange">
                                    <i class="bi bi-calendar3 me-1"></i> Filter Tanggal Verifikasi
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="daterange" 
                                       name="daterange"
                                       autocomplete="off"
                                       placeholder="Pilih rentang tanggal verifikasi"
                                       value="{{ request('start_date') && request('end_date') ? date('d/m/Y', strtotime(request('start_date'))) . ' - ' . date('d/m/Y', strtotime(request('end_date'))) : '' }}">
                                <input type="hidden" name="start_date" id="start_date" value="{{ request('start_date') }}">
                                <input type="hidden" name="end_date" id="end_date" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-fill">
                                        <i class="bi bi-funnel-fill me-1"></i> Terapkan Filter
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary flex-fill" id="resetFilter">
                                        <i class="bi bi-arrow-clockwise me-1"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="user-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th class="text-start">Email</th>
                                    <th class="text-start">Nama</th>
                                    <th class="text-start">NPWPD</th>
                                    <th class="text-start">Kabupaten</th>
                                    <th class="text-start">Nomor Telepon</th>
                                    <th class="text-start">Tanggal Verifikasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-start">{{ $user->email }}</td>
                                        <td class="text-start">{{ $user->name }}</td>
                                        <td class="text-start">{{ $user->userDetail->npwpd ?? '-' }}</td>
                                        <td class="text-start">{{ $user->userDetail->kabupaten->nama ?? '-' }}</td>
                                        <td class="text-start">{{ $user->userDetail->nomor_telepon ?? '-' }}</td>
                                        <td class="text-start">
                                            {{ $user->userDetail->verified_at ? \Carbon\Carbon::parse($user->userDetail->verified_at)->locale('id')->isoFormat('LL') : '-' }}
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
            // Initialize DataTable
            $('#user-table').DataTable({
                "responsive": true,
                "language": {
                    "url": '{{ asset('assets/vendors/datatables-lang-id.json') }}'
                }
            });

            // Initialize daterangepicker
            $('#daterange').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    format: 'DD/MM/YYYY',
                    separator: ' - ',
                    applyLabel: 'Terapkan',
                    cancelLabel: 'Batal',
                    fromLabel: 'Dari',
                    toLabel: 'Sampai',
                    customRangeLabel: 'Custom',
                    weekLabel: 'W',
                    daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                    monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                    firstDay: 1
                }
            });

            // Update input when date range is selected
            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                $('#start_date').val(picker.startDate.format('YYYY-MM-DD'));
                $('#end_date').val(picker.endDate.format('YYYY-MM-DD'));
            });

            // Clear input when date range is cancelled
            $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                $('#start_date').val('');
                $('#end_date').val('');
            });

            // Reset filter button
            $('#resetFilter').on('click', function() {
                $('#daterange').val('');
                $('#start_date').val('');
                $('#end_date').val('');
                window.location.href = '{{ route('master-data.user.index') }}';
            });
        });
    </script>
@endpush
