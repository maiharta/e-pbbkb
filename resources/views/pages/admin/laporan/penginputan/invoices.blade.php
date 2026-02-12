@extends('layouts.dashboard-base')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6">
                    <h3>Data Invoice</h3>
                    <p class="text-muted">{{ $pelaporan->user->name }} - {{ $pelaporan->bulan_name }} {{ $pelaporan->tahun }}</p>
                </div>
                <div class="col-12 col-md-6">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('laporan.penginputan.index') }}">Laporan Penginputan</a></li>
                            <li class="breadcrumb-item active">Data Invoice</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="invoice-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nomor Invoice</th>
                                    <th>Nama Customer</th>
                                    <th>NPWPD</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Tanggal Transaksi</th>
                                    <th>Jatuh Tempo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pelaporan->invoices as $invoice)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $invoice->invoice_number }}</td>
                                        <td>{{ $invoice->customer_name }}</td>
                                        <td>{{ $invoice->customer_npwpd }}</td>
                                        <td class="text-end">Rp {{ number_format($invoice->amount, 2, ',', '.') }}</td>
                                        <td>
                                            @if($invoice->payment_status == 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($invoice->payment_status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($invoice->payment_status == 'expired')
                                                <span class="badge bg-danger">Expired</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $invoice->payment_status }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $invoice->sipay_transaction_date ? Carbon\Carbon::parse($invoice->sipay_transaction_date)->format('d/m/Y H:i') : '-' }}</td>
                                        <td>{{ $invoice->expires_at ? Carbon\Carbon::parse($invoice->expires_at)->endOfDay()->format('d/m/Y H:i') : '-' }}</td>
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
        $('#invoice-table').DataTable({
            "language": {
                "url": '{{ asset("assets/vendors/datatables-lang-id.json") }}'
            },
            "order": [[6, 'desc']]
        });
    </script>
@endpush
