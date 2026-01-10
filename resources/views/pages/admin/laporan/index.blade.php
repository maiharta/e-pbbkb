@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Laporan</h3>
                </div>
            </div>
        </div>
    </div>
    <section class="section">
        {{-- Realisasi PBBKB --}}
        <div class="card filter-card">
            <div class="card-header">
                <h5 class="card-title">Realisasi PBBKB</h5>
            </div>
            <div class="card-body">
                <!-- Add alert for validation errors -->
                <div class="alert alert-danger"
                     id="validationAlert"
                     style="display: none;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <span id="validationMessage"></span>
                </div>

                <form action="#"
                      id="filterForm"
                      method="GET">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <input id="periode_awal"
                                       name="periode_awal"
                                       type="hidden">
                                <label class="form-label fw-bold"
                                       for="periode_awal_picker">Periode awal</label>
                                <input class="form-control"
                                       id="periode_awal_picker"
                                       name="periode_awal_picker"
                                       placeholder="Masukkan periode awal"
                                       type="text">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <input id="periode_akhir"
                                       name="periode_akhir"
                                       type="hidden">
                                <label class="form-label fw-bold"
                                       for="periode_akhir_picker">Periode akhir</label>
                                <input class="form-control"
                                       id="periode_akhir_picker"
                                       name="periode_akhir_picker"
                                       placeholder="Masukkan periode akhir"
                                       type="text">
                            </div>
                        </div>
                        <x-input.select :multiple="true"
                                        :options="$kabupatens->map(
                                            fn($item) => ['key' => $item->id, 'value' => $item->nama],
                                        )"
                                        label="Kabupaten"
                                        name="kabupaten_id"
                                        placeholder="Semua kabupaten"
                                        value="{{ old('kabupaten_id') }}" />
                    </div>
                    <div class="gap-2 d-flex">
                        <button class="w-100 d-block btn btn-secondary"
                                id="resetFilter"
                                type="button">
                            <i class="bi bi-x-circle me-1"></i> Reset Filter
                        </button>

                        <button class="w-100 d-block btn btn-primary"
                                id="exportExcel"
                                type="button">
                            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Export Pelaporan --}}
        <div class="card filter-card mt-3">
            <div class="card-header">
                <h5 class="card-title">Export Pelaporan</h5>
            </div>
            <div class="card-body">
                <!-- Add alert for validation errors -->
                <div class="alert alert-danger"
                     id="validationAlertPelaporan"
                     style="display: none;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <span id="validationMessagePelaporan"></span>
                </div>

                <form action="#"
                      id="filterFormPelaporan"
                      method="GET">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <input id="periode_awal_pelaporan"
                                       name="periode_awal_pelaporan"
                                       type="hidden">
                                <label class="form-label fw-bold"
                                       for="periode_awal_pelaporan_picker">Periode awal</label>
                                <input class="form-control"
                                       id="periode_awal_pelaporan_picker"
                                       name="periode_awal_pelaporan_picker"
                                       placeholder="Masukkan periode awal"
                                       type="text">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <input id="periode_akhir_pelaporan"
                                       name="periode_akhir_pelaporan"
                                       type="hidden">
                                <label class="form-label fw-bold"
                                       for="periode_akhir_pelaporan_picker">Periode akhir</label>
                                <input class="form-control"
                                       id="periode_akhir_pelaporan_picker"
                                       name="periode_akhir_pelaporan_picker"
                                       placeholder="Masukkan periode akhir"
                                       type="text">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold"
                                       for="status">Status</label>
                                <select class="form-select"
                                        id="status"
                                        name="status">
                                    <option value="">Semua Status</option>
                                    <option value="verified">Terverifikasi</option>
                                    <option value="ongoing">Berjalan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="gap-2 d-flex">
                        <button class="w-100 d-block btn btn-secondary"
                                id="resetFilterPelaporan"
                                type="button">
                            <i class="bi bi-x-circle me-1"></i> Reset Filter
                        </button>

                        <button class="w-100 d-block btn btn-primary"
                                id="exportExcelPelaporan"
                                type="button">
                            <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Pelaporan --}}

    </section>
@endsection

@push('scripts')
    <script type="module">
        // Initialize date pickers
        const startPicker = flatpickr("#periode_awal_picker", {
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

                // Set the minimum date for end period
                if (selectedDates[0]) {
                    endPicker.set('minDate', selectedDates[0]);
                }

                // Validate dates whenever start date changes
                validateDates();
            }
        });

        const endPicker = flatpickr("#periode_akhir_picker", {
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

                // Validate dates whenever end date changes
                validateDates();
            }
        });

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
            // Reset all form fields
            $('#filterForm')[0].reset();

            // Clear flatpickr instances
            startPicker.clear();
            endPicker.clear();

            // Reset hidden inputs
            $('#periode_awal').val('');
            $('#periode_akhir').val('');

            // Reset select2 dropdowns if using them
            if ($.fn.select2) {
                $('select[name="kabupaten_id[]"]').val(null).trigger('change');
            }

            // Hide validation message
            hideValidationError();

            // Hide results section
            $('#resultsSection').hide();
        });

        // Apply filter
        $('#applyFilter').on('click', function() {
            // Perform validation
            if (!validateDates()) {
                return false;
            }

            // Check if start and end periods are selected
            // if (!$('#periode_awal').val() || !$('#periode_akhir').val()) {
            //     showValidationError("Periode awal dan periode akhir harus diisi.");
            //     return false;
            // }

            // If validation passes, fetch and display results
            fetchResults();
        });

        // Export Excel
        $('#exportExcel').on('click', function() {
            // Perform validation before export
            if (!validateDates()) {
                return false;
            }

            // Get form data
            const formData = $('#filterForm').serialize();

            // Redirect to export URL with form data
            window.location.href = "{{ route('laporan.export-excel') }}?" + formData;
        });

        // ===== Export Pelaporan Section =====

        // Initialize date pickers for Pelaporan
        const startPickerPelaporan = flatpickr("#periode_awal_pelaporan_picker", {
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
                $('#periode_awal_pelaporan').val(formattedDate);

                // Set the minimum date for end period
                if (selectedDates[0]) {
                    endPickerPelaporan.set('minDate', selectedDates[0]);
                }

                // Validate dates whenever start date changes
                validateDatesPelaporan();
            }
        });

        const endPickerPelaporan = flatpickr("#periode_akhir_pelaporan_picker", {
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
                $('#periode_akhir_pelaporan').val(formattedDate);

                // Validate dates whenever end date changes
                validateDatesPelaporan();
            }
        });

        // Date validation function for Pelaporan
        function validateDatesPelaporan() {
            const startDate = startPickerPelaporan.selectedDates[0];
            const endDate = endPickerPelaporan.selectedDates[0];

            if (startDate && endDate) {
                if (startDate > endDate) {
                    showValidationErrorPelaporan("Periode awal tidak boleh lebih besar dari periode akhir.");
                    return false;
                }
            }

            hideValidationErrorPelaporan();
            return true;
        }

        function showValidationErrorPelaporan(message) {
            $('#validationMessagePelaporan').text(message);
            $('#validationAlertPelaporan').show();
        }

        function hideValidationErrorPelaporan() {
            $('#validationAlertPelaporan').hide();
        }

        // Reset filter for Pelaporan
        $('#resetFilterPelaporan').on('click', function() {
            // Reset all form fields
            $('#filterFormPelaporan')[0].reset();

            // Clear flatpickr instances
            startPickerPelaporan.clear();
            endPickerPelaporan.clear();

            // Reset hidden inputs
            $('#periode_awal_pelaporan').val('');
            $('#periode_akhir_pelaporan').val('');

            // Reset status select
            $('#status').val('');

            // Hide validation message
            hideValidationErrorPelaporan();
        });

        // Export Excel for Pelaporan
        $('#exportExcelPelaporan').on('click', function() {
            // Perform validation before export
            if (!validateDatesPelaporan()) {
                return false;
            }

            // Get form data
            const formData = $('#filterFormPelaporan').serialize();

            // Redirect to export URL with form data
            window.location.href = "{{ route('laporan.export-pelaporan-excel') }}?" + formData;
        });
    </script>
@endpush
