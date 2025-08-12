@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Data Transaksi</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb"
                         class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">
                                    Data Transaksi</a>
                            </li>
                            <li aria-current="page"
                                class="breadcrumb-item active">Invoice Pelaporan {{ $pelaporan->bulan_name }} -
                                {{ $pelaporan->tahun }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <table class="table"
                           id="table">
                        <thead>
                            <tr>
                                <th class="text-start">No</th>
                                <th>Nomor Invoice</th>
                                <th>Total</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pelaporan->invoices as $invoice)
                                <tr>
                                    <td class="text-start">{{ $loop->iteration }}</td>
                                    <td>{{ $invoice->invoice_number }}</td>
                                    <td>Rp. {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        {!! $invoice->status_badge !!}
                                    </td>
                                    <td class="text-center">
                                        @if ($invoice->payment_status == 'pending')
                                            <button class="btn btn-sm btn-info view-invoice"
                                                    data-bs-target="#invoiceDetailModal"
                                                    data-bs-toggle="modal"
                                                    data-invoice-id="{{ $invoice->ulid }}">
                                                <i class="bi bi-eye"></i> Detail
                                            </button>
                                        @else
                                            <span class="text-muted">Tidak tersedia</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <!-- Invoice Detail Modal -->
    <div aria-hidden="true"
         aria-labelledby="invoiceDetailModalLabel"
         class="modal fade"
         id="invoiceDetailModal"
         tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white"
                        id="invoiceDetailModalLabel">
                        Detail Invoice
                    </h5>
                    <button aria-label="Close"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                            type="button"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center py-4"
                         id="loadingSpinner">
                        <div class="spinner-border text-primary"
                             role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat data invoice...</p>
                    </div>

                    <div class="d-none"
                         id="invoiceDetailContent">
                        <!-- Invoice Header -->
                        <div class="border-bottom pb-3 mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 class="fw-bold text-primary mb-0"
                                    id="invoice-number"></h4>
                                <span id="invoice-status"></span>
                            </div>
                            <p class="text-muted mb-0">
                                <small>Dibuat: <span id="invoice-created"></span></small>
                            </p>
                        </div>

                        <!-- Main Content -->
                        <div class="row g-4">
                            <!-- Left Column -->
                            <div class="col-lg-6">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold mb-3">
                                            <i class="bi bi-receipt me-2 text-primary"></i>
                                            Informasi Invoice
                                        </h6>
                                        <table class="table table-borderless table-sm mb-0">
                                            <tr>
                                                <td class="text-muted">Total Pembayaran</td>
                                                <td class="fw-bold text-end"
                                                    id="invoice-amount"></td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">Tanggal Kadaluarsa</td>
                                                <td class="text-end text-danger fw-bold"
                                                    id="invoice-expires"></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-lg-6">
                                <div class="card bg-light shadow-sm h-100">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold mb-3">
                                            <i class="bi bi-credit-card me-2 text-primary"></i>
                                            Informasi Pembayaran
                                        </h6>
                                        <div class="mb-3">
                                            <label class="text-muted mb-1 d-block">Virtual Account:</label>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-white border rounded p-2 flex-grow-1">
                                                    <span class="fw-bold fs-5 d-block text-center"
                                                          id="invoice-va"></span>
                                                </div>
                                                <button class="btn btn-outline-primary ms-2 d-flex align-items-center justify-content-center"
                                                        id="copy-va-btn"
                                                        onclick="copyVAToClipboard()"
                                                        style="min-width: 40px; height: 40px;"
                                                        title="Salin ke clipboard">
                                                    <i class="bi bi-clipboard"
                                                       style="height: 1.6rem;"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <p class="mb-3">
                                            <span class="text-muted">Bank:</span>
                                            <span class="fw-bold"
                                                  id="invoice-bank">Bank Pembangunan Daerah Bali</span>
                                        </p>
                                        <button class="btn btn-info btn-sm w-100"
                                                data-bs-toggle="modal"
                                                data-bs-target="#paymentInstructionsModal">
                                            <i class="bi bi-info-circle me-2"></i>
                                            Tata Cara Pembayaran
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Customer Info -->
                            <div class="col-12">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold mb-3">
                                            <i class="bi bi-person me-2 text-primary"></i>
                                            Informasi Wajib Pajak
                                        </h6>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <table class="table table-borderless table-sm mb-0">
                                                    <tr>
                                                        <td class="text-muted"
                                                            width="120">Nama</td>
                                                        <td class="fw-bold"
                                                            id="customer-name"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">NPWPD</td>
                                                        <td id="customer-npwpd"></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-lg-6">
                                                <table class="table table-borderless table-sm mb-0">
                                                    <tr>
                                                        <td class="text-muted"
                                                            width="120">Email</td>
                                                        <td id="customer-email"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">No. Telepon</td>
                                                        <td id="customer-phone"></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold mb-3">
                                            <i class="bi bi-info-circle me-2 text-primary"></i>
                                            Deskripsi
                                        </h6>
                                        <p class="text-muted mb-0"
                                           id="invoice-description"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-danger d-none"
                         id="errorMessage">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Terjadi kesalahan saat memuat data invoice.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Instructions Modal -->
    <div aria-hidden="true"
         aria-labelledby="paymentInstructionsModalLabel"
         class="modal fade"
         id="paymentInstructionsModal"
         tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title text-white" id="paymentInstructionsModalLabel">
                        <i class="bi bi-info-circle me-2"></i>
                        Tata Cara Pembayaran E-PBBKB Provinsi Bali
                    </h5>
                    <button aria-label="Close"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                            type="button"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Virtual Account Format -->
                    <div class="card mb-2">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0 fw-bold">
                                <i class="bi bi-credit-card me-2"></i>
                                FORMAT VIRTUAL ACCOUNT E-PBBKB PROVINSI BALI
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center py-2">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Format:</strong> 129 + 5325 + Nomor ID/TAGIHAN</p>
                                    <p class="mb-0"><strong>Nomor ID/TAGIHAN:</strong> <span id="sample-invoice-id" class="fw-bold text-primary"></span></p>
                                </div>
                                {{-- <div class="col-md-6">
                                    <div class="bg-light p-3 rounded text-center">
                                        <small class="text-muted d-block">Virtual Account Anda:</small>
                                        <span class="fs-4 fw-bold text-primary" id="full-va-example"></span>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>

                    <!-- Payment Methods with Accordion/Collapse -->
                    <div class="accordion" id="paymentMethodsAccordion">
                        <!-- Method A -->
                        <div class="accordion-item mb-3">
                            <h2 class="accordion-header" id="headingMethodA">
                                <button class="accordion-button collapsed bg-success text-white fw-bold"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapseMethodA"
                                        aria-expanded="false"
                                        aria-controls="collapseMethodA">
                                    A. MOBILE / INTERNET BANKING BANK BPD BALI
                                </button>
                            </h2>
                            <div id="collapseMethodA"
                                 class="accordion-collapse collapse"
                                 aria-labelledby="headingMethodA"
                                 data-bs-parent="#paymentMethodsAccordion">
                                <div class="accordion-body">
                                    <ol class="list-unstyled">
                                        <li class="mb-2"><span class="badge bg-success me-2">1</span>Masukkan username dan password / PIN pada aplikasi Mobile / Internet Banking.</li>
                                        <li class="mb-2"><span class="badge bg-success me-2">2</span>Pilih menu <strong>"Pembayaran"</strong></li>
                                        <li class="mb-2"><span class="badge bg-success me-2">3</span>Pilih <strong>"Pembayaran Retribusi"</strong></li>
                                        <li class="mb-2"><span class="badge bg-success me-2">4</span>Pilih Input Nomor ID lalu Pilih <strong>"Retribusi OPD Pemprov Bali"</strong></li>
                                        <li class="mb-2"><span class="badge bg-success me-2">5</span>Input Nomor ID/tagihan <strong><span class="sample-id"></span></strong></li>
                                        <li class="mb-2"><span class="badge bg-success me-2">6</span>Input PIN untuk melanjutkan transaksi.</li>
                                        <li class="mb-0"><span class="badge bg-success me-2">7</span>Selesai.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- Method B -->
                        <div class="accordion-item mb-3">
                            <h2 class="accordion-header" id="headingMethodB">
                                <button class="accordion-button collapsed bg-success text-white fw-bold"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapseMethodB"
                                        aria-expanded="false"
                                        aria-controls="collapseMethodB">
                                    B. PEMBAYARAN DARI ATM BERSAMA
                                </button>
                            </h2>
                            <div id="collapseMethodB"
                                 class="accordion-collapse collapse"
                                 aria-labelledby="headingMethodB"
                                 data-bs-parent="#paymentMethodsAccordion">
                                <div class="accordion-body">
                                    <ol class="list-unstyled">
                                        <li class="mb-2"><span class="badge bg-success me-2">1</span>Pilih Bahasa</li>
                                        <li class="mb-2"><span class="badge bg-success me-2">2</span>Masukkan PIN</li>
                                        <li class="mb-2"><span class="badge bg-success me-2">3</span>Pilih <strong>"Transaksi Lainnya"</strong></li>
                                        <li class="mb-2"><span class="badge bg-success me-2">4</span>Pilih <strong>"Transfer"</strong></li>
                                        <li class="mb-2"><span class="badge bg-success me-2">5</span>Pilih <strong>"Ke Rekening Bank Lain"</strong></li>
                                        <li class="mb-2"><span class="badge bg-success me-2">6</span>Masukkan nomor rekening tujuan <strong><span class="full-va"></span></strong> (No Virtual Account), lalu tekan "Benar/Lanjut"</li>
                                        <li class="mb-2"><span class="badge bg-success me-2">7</span>Input nominal yang ingin ditransfer sesuai tagihan yang ingin dibayar, lalu tekan "Benar/Lanjut".</li>
                                        <li class="mb-2"><span class="badge bg-success me-2">8</span>Silakan isi atau kosongkan nomor referensi transfer kemudian tekan "Benar"</li>
                                        <li class="mb-2"><span class="badge bg-success me-2">9</span>Muncul Layar Konfirmasi Transfer yang berisi nomor rekening tujuan bank beserta jumlah yang dibayar.</li>
                                        <li class="mb-2"><span class="badge bg-success me-2">10</span>Jika sudah benar, Tekan "Benar".</li>
                                        <li class="mb-0"><span class="badge bg-success me-2">11</span>Selesai</li>
                                    </ol>
                                    <div class="alert alert-info mt-3">
                                        <small><strong>Note:</strong> Tata cara tiap bank mungkin ada sedikit berbeda namun secara gambaran umum sama seperti penjelasan diatas.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Method C -->
                        <div class="accordion-item mb-3">
                            <h2 class="accordion-header" id="headingMethodC">
                                <button class="accordion-button collapsed bg-success text-white fw-bold"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapseMethodC"
                                        aria-expanded="false"
                                        aria-controls="collapseMethodC">
                                    C. PEMBAYARAN DARI KLIRING BANK LAIN
                                </button>
                            </h2>
                            <div id="collapseMethodC"
                                 class="accordion-collapse collapse"
                                 aria-labelledby="headingMethodC"
                                 data-bs-parent="#paymentMethodsAccordion">
                                <div class="accordion-body">
                                    <ol class="list-unstyled">
                                        <li class="mb-2"><span class="badge bg-success me-2">1</span>Pilih <strong>"Bank BPD Bali"</strong> sebagai bank tujuan.</li>
                                        <li class="mb-2"><span class="badge bg-success me-2">2</span>Masukkan nomor rekening tujuan <strong><span class="full-va"></span></strong> (No Virtual Account).</li>
                                        <li class="mb-2"><span class="badge bg-success me-2">3</span>Input nominal yang ingin ditransfer sesuai tagihan yang ingin dibayar. Mohon dipastikan nominal yang akan ditransfer sama dengan jumlah tagihan yang harus dibayar agar proses bisa berjalan sukses.</li>
                                        <li class="mb-2"><span class="badge bg-success me-2">4</span>Lanjutkan transaksi.</li>
                                        <li class="mb-0"><span class="badge bg-success me-2">5</span>Selesai</li>
                                    </ol>
                                    <div class="alert alert-info mt-3">
                                        <small><strong>Note:</strong> Tata cara tiap bank mungkin ada sedikit berbeda namun secara gambaran umum sama seperti penjelasan diatas.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Method D -->
                        <div class="accordion-item mb-3">
                            <h2 class="accordion-header" id="headingMethodD">
                                <button class="accordion-button collapsed bg-success text-white fw-bold"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapseMethodD"
                                        aria-expanded="false"
                                        aria-controls="collapseMethodD">
                                    D. MOBILE / INTERNET BANKING BANK LAIN
                                </button>
                            </h2>
                            <div id="collapseMethodD"
                                 class="accordion-collapse collapse"
                                 aria-labelledby="headingMethodD"
                                 data-bs-parent="#paymentMethodsAccordion">
                                <div class="accordion-body">
                                    <ol class="list-unstyled">
                                        <li class="mb-2"><span class="badge bg-success me-2">1</span>Login pada aplikasi Mobile / Internet Banking Anda</li>
                                        <li class="mb-2"><span class="badge bg-success me-2">2</span>Pilih menu <strong>"Transfer"</strong></li>
                                        <li class="mb-2"><span class="badge bg-success me-2">3</span>Pilih menu <strong>"Transfer Antar Bank"</strong></li>
                                        <li class="mb-2"><span class="badge bg-success me-2">4</span>Pilih menu <strong>"Transfer Online Antar Bank (Biaya Rp 6.500,-)"</strong> bukan BIFAST</li>
                                        <li class="mb-2"><span class="badge bg-success me-2">5</span>Pilih bank tujuan <strong>"Bank BPD Bali"</strong></li>
                                        <li class="mb-2"><span class="badge bg-success me-2">6</span>Masukkan nomor rekening tujuan <strong><span class="shortened-va"></span></strong> (No Virtual Account).</li>
                                        <li class="mb-2"><span class="badge bg-success me-2">7</span>Input nominal yang ingin ditransfer sesuai tagihan yang ingin dibayar, lalu tekan "Benar"</li>
                                        <li class="mb-2"><span class="badge bg-success me-2">8</span>Muncul Layar Konfirmasi Transfer yang berisi nomor rekening tujuan bank beserta jumlah yang dibayar</li>
                                        <li class="mb-2"><span class="badge bg-success me-2">9</span>Masukkan Password atau PIN</li>
                                        <li class="mb-0"><span class="badge bg-success me-2">10</span>Selesai</li>
                                    </ol>
                                    <div class="alert alert-info mt-3">
                                        <small><strong>Note:</strong> Tata cara tiap bank mungkin ada sedikit berbeda namun secara gambaran umum sama seperti penjelasan diatas.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer with Back Button -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-4 border-top">
                        <button type="button"
                                class="btn btn-outline-secondary"
                                data-bs-dismiss="modal"
                                onclick="showInvoiceDetail()">
                            <i class="bi bi-arrow-left me-2"></i>
                            Kembali ke Detail Invoice
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#table').DataTable();

            // Handle click on view invoice button
            $('.view-invoice').on('click', function() {
                const invoiceId = $(this).data('invoice-id');

                // Reset modal state
                $('#loadingSpinner').removeClass('d-none');
                $('#invoiceDetailContent').addClass('d-none');
                $('#errorMessage').addClass('d-none');

                // Fetch invoice details via AJAX
                $.ajax({
                    url: '{{ route('invoices.show-invoice') }}/' + invoiceId,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            const invoice = response.data;

                            // Populate invoice details
                            $('#invoice-number').text(invoice.invoice_number);
                            $('#invoice-amount').text(
                                `Rp ${new Intl.NumberFormat('id-ID').format(invoice.amount)}`
                            );
                            $('#invoice-status').html(getStatusBadge(invoice.payment_status));
                            $('#invoice-created').text(formatDate(invoice
                                .sipay_transaction_date));
                            $('#invoice-expires').text(invoice.next_due_date);
                            $('#invoice-va').text(invoice.sipay_virtual_account ||
                                'Belum tersedia');
                            $('#invoice-description').text(invoice.description ||
                                'Tidak ada deskripsi');

                            // Customer info
                            $('#customer-name').text(invoice.customer_name);
                            $('#customer-npwpd').text(invoice.customer_npwpd);
                            $('#customer-email').text(invoice.customer_email);
                            $('#customer-phone').text(invoice.customer_phone || '-');

                            // Payment instructions link
                            $('#paymentInstructionsBtn').attr('href',
                                `/operator/transactions/${invoice.pelaporan_id}/invoice/${invoice.id}/payment-instructions`
                            );

                            // Populate payment instructions modal
                            populatePaymentInstructions(invoice);

                            // Show content
                            $('#loadingSpinner').addClass('d-none');
                            $('#invoiceDetailContent').removeClass('d-none');
                        } else {
                            showError();
                        }
                    },
                    error: function() {
                        showError();
                    }
                });
            });

            function showError() {
                $('#loadingSpinner').addClass('d-none');
                $('#errorMessage').removeClass('d-none');
            }

            function formatDate(dateString) {
                if (!dateString) return '-';

                const options = {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                };

                return new Date(dateString).toLocaleDateString('id-ID', options);
            }

            function getStatusBadge(status) {
                switch (status) {
                    case 'paid':
                        return '<span class="badge bg-success">Lunas</span>';
                    case 'pending':
                        return '<span class="badge bg-warning text-dark">Menunggu Pembayaran</span>';
                    case 'expired':
                        return '<span class="badge bg-danger">Kadaluarsa</span>';
                    default:
                        return '<span class="badge bg-secondary">Tidak Diketahui</span>';
                }
            }

            function populatePaymentInstructions(invoice) {
                const invoiceId = invoice.sipay_invoice || '-';
                const fullVA = `1295325${invoice.sipay_invoice || invoiceId}`;
                const shortenedVA = `5325${invoice.sipay_invoice || invoiceId}`;

                // Populate invoice ID examples
                $('#sample-invoice-id').text(invoiceId);
                $('#full-va-example').text(fullVA);

                // Populate all VA references in payment instructions
                $('.sample-id').text(invoiceId);
                $('.full-va').text(fullVA);
                $('.shortened-va').text(shortenedVA);
            }
        });

        function copyVAToClipboard() {
            const vaNumber = document.getElementById('invoice-va').innerText;

            // Don't copy if the text is "Belum tersedia"
            if (vaNumber === 'Belum tersedia') {
                return;
            }

            // Use the modern Clipboard API if available
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(vaNumber)
                    .then(() => showCopySuccess())
                    .catch(err => {
                        console.error('Failed to copy: ', err);
                        // Fallback to the older method
                        fallbackCopyTextToClipboard(vaNumber);
                    });
            } else {
                // Fallback for browsers that don't support the Clipboard API
                fallbackCopyTextToClipboard(vaNumber);
            }
        }

        function fallbackCopyTextToClipboard(text) {
            // Create a temporary textarea element
            const textarea = document.createElement('textarea');
            textarea.value = text;

            // Make the textarea out of viewport
            textarea.style.position = 'fixed';
            textarea.style.left = '-999999px';
            textarea.style.top = '-999999px';
            document.body.appendChild(textarea);

            textarea.focus();
            textarea.select();

            let success = false;
            try {
                success = document.execCommand('copy');
            } catch (err) {
                console.error('Failed to copy text: ', err);
            }

            document.body.removeChild(textarea);

            if (success) {
                showCopySuccess();
            }
        }

        function showCopySuccess() {
            // Show success feedback
            const copyBtn = document.getElementById('copy-va-btn');
            const originalHTML = copyBtn.innerHTML;

            copyBtn.innerHTML = '<i class="bi bi-check2"></i>';
            copyBtn.classList.remove('btn-outline-primary');
            copyBtn.classList.add('btn-success');

            // Reset button after 2 seconds
            setTimeout(() => {
                copyBtn.innerHTML = originalHTML;
                copyBtn.classList.remove('btn-success');
                copyBtn.classList.add('btn-outline-primary');
            }, 2000);
        }

        function showInvoiceDetail() {
            // Close payment instructions modal and show invoice detail modal
            const paymentModal = bootstrap.Modal.getInstance(document.getElementById('paymentInstructionsModal'));
            if (paymentModal) {
                paymentModal.hide();
            }

            // Show invoice detail modal after a brief delay to ensure smooth transition
            setTimeout(() => {
                const invoiceModal = new bootstrap.Modal(document.getElementById('invoiceDetailModal'));
                invoiceModal.show();
            }, 300);
        }
    </script>
@endpush
