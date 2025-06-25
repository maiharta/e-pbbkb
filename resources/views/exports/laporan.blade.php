<table>
    <tr>
        <th colspan="9"
            style="text-align: center;font-weight: bold;">LAPORAN REKAPITULASI PAJAK BAHAN BAKAR KENDARAAN BERMOTOR
            (PBBKB)</th>
    </tr>
    <tr></tr>
</table>
<table border="1"
       cellpadding="5"
       cellspacing="0"
       style="border-collapse: collapse;">
    <tr>
        <th style="font-weight: bold; text-align: center;border: 1px solid #000000;">No</th>
        <th style="font-weight: bold; text-align: center;border: 1px solid #000000;">Nama Perusahaan</th>
        <th style="font-weight: bold; text-align: center;border: 1px solid #000000;">Jenis BBM</th>
        <th style="font-weight: bold; text-align: center;border: 1px solid #000000;">Volume Pembelian (Liter)</th>
        <th style="font-weight: bold; text-align: center;border: 1px solid #000000;">Volume Penjualan (Liter)</th>
        <th style="font-weight: bold; text-align: center;border: 1px solid #000000;">Dasar Pengenaan Pajak (Rp)</th>
        <th style="font-weight: bold; text-align: center;border: 1px solid #000000;">Total PBBKB (Rp)</th>
        <th style="font-weight: bold; text-align: center;border: 1px solid #000000;">Sanksi Administrasi (Rp)</th>
        <th style="font-weight: bold; text-align: center;border: 1px solid #000000;">Subtotal Keseluruhan (Rp)</th>
    </tr>
    @php
        // Helper function to format numbers for Excel without losing zeros
        function formatNumber($number)
        {
            if ($number == 0) {
                return '0';
            }
            return $number;
        }
        // Initialize variables for grand totals
        $grandTotalVolumePembelian = 0;
        $grandTotalVolumePenjualan = 0;
        $grandTotalDpp = 0;
        $grandTotalPbbkb = 0;
        $grandTotalSanksi = 0;
        $grandTotalSubtotal = 0;

        // Arrays to track BBM types across all reports
        $allBbmTypes = [];
        foreach ($data as $userData) {
            foreach ($userData['jenis_bbm_totals'] as $jenisBbmId => $jenisBbm) {
                if (!isset($allBbmTypes[$jenisBbmId])) {
                    $allBbmTypes[$jenisBbmId] = [
                        'nama_jenis_bbm' => $jenisBbm['nama_jenis_bbm'],
                        'total_volume_pembelian' => 0,
                        'total_volume_penjualan' => 0,
                        'total_dpp_penjualan' => 0,
                        'total_pbbkb_penjualan' => 0,
                        'total_sanksi' => 0,
                        'subtotal' => 0,
                    ];
                }

                // Aggregate by BBM type
                $allBbmTypes[$jenisBbmId]['total_volume_pembelian'] += $jenisBbm['total_volume_pembelian'];
                $allBbmTypes[$jenisBbmId]['total_volume_penjualan'] += $jenisBbm['total_volume_penjualan'];
                $allBbmTypes[$jenisBbmId]['total_dpp_penjualan'] += $jenisBbm['total_dpp_penjualan'];
                $allBbmTypes[$jenisBbmId]['total_pbbkb_penjualan'] += $jenisBbm['total_pbbkb_penjualan'];
                // $allBbmTypes[$jenisBbmId]['total_sanksi'] += ($jenisBbm['sanksi'] ?? 0);
                // $allBbmTypes[$jenisBbmId]['subtotal'] += $jenisBbm['subtotal'];
            }

            // Add to grand totals
            $grandTotalSanksi += $userData['total_sanksi_perusahaan'];
            $grandTotalSubtotal += $userData['total_subtotal_perusahaan'];
        }
    @endphp

    @foreach ($data as $userKey => $userData)
        @php
            $jenisTotalCount = count($userData['jenis_bbm_totals']);
            $totalVolumePembelian = 0;
            $totalVolumePenjualan = 0;
            $totalDpp = 0;
            $totalPbbkb = 0;
            $firstItem = true;
        @endphp

        @foreach ($userData['jenis_bbm_totals'] as $jenisBbmId => $jenisBBM)
            @php
                $totalVolumePembelian += $jenisBBM['total_volume_pembelian'];
                $totalVolumePenjualan += $jenisBBM['total_volume_penjualan'];
                $totalDpp += $jenisBBM['total_dpp_penjualan'];
                $totalPbbkb += $jenisBBM['total_pbbkb_penjualan'];

                // Add to grand totals (but only for the BBM-specific columns)
                $grandTotalVolumePembelian += $jenisBBM['total_volume_pembelian'];
                $grandTotalVolumePenjualan += $jenisBBM['total_volume_penjualan'];
                $grandTotalDpp += $jenisBBM['total_dpp_penjualan'];
                $grandTotalPbbkb += $jenisBBM['total_pbbkb_penjualan'];
            @endphp
            <tr>
                @if ($firstItem)
                    <td rowspan="{{ $jenisTotalCount + 1 }}"
                        style="text-align:center;vertical-align: middle;border: 1px solid #000000;">
                        {{ $loop->parent->iteration }}</td>
                    <td rowspan="{{ $jenisTotalCount + 1 }}"
                        style="text-align:left;vertical-align: middle;border: 1px solid #000000;">
                        {{ $userData['user']->name }}</td>
                    @php $firstItem = false; @endphp
                @endif
                <td style="border: 1px solid #000000;">{{ $jenisBBM['nama_jenis_bbm'] }}</td>
                <td style="text-align: right;border: 1px solid #000000;">
                    {{ formatNumber($jenisBBM['total_volume_pembelian']) }}</td>
                <td style="text-align: right;border: 1px solid #000000;">
                    {{ formatNumber($jenisBBM['total_volume_penjualan']) }}</td>
                <td style="text-align: right;border: 1px solid #000000;">
                    {{ formatNumber($jenisBBM['total_dpp_penjualan']) }}</td>
                <td style="text-align: right;border: 1px solid #000000;">
                    {{ formatNumber($jenisBBM['total_pbbkb_penjualan']) }}</td>

                @if ($loop->first)
                    <td rowspan="{{ $jenisTotalCount+1 }}"
                        style="text-align: center;vertical-align: middle;font-weight:bold;border: 1px solid #000000;">
                        {{ formatNumber($userData['total_sanksi_perusahaan']) }}</td>
                    <td rowspan="{{ $jenisTotalCount+1 }}"
                        style="text-align: center;vertical-align: middle;font-weight:bold;border: 1px solid #000000;">
                        {{ formatNumber($userData['total_subtotal_perusahaan']) }}</td>
                @endif
            </tr>
        @endforeach

        <tr>
            <td style="font-weight: bold;border: 1px solid #000000;background-color: #F2F2F2;">Jumlah</td>
            <td style="font-weight: bold;text-align: right;border: 1px solid #000000;background-color: #F2F2F2;">{{ formatNumber($totalVolumePembelian) }}</td>
            <td style="font-weight: bold;text-align: right;border: 1px solid #000000;background-color: #F2F2F2;">{{ formatNumber($totalVolumePenjualan) }}</td>
            <td style="font-weight: bold;text-align: right;border: 1px solid #000000;background-color: #F2F2F2;">{{ formatNumber($totalDpp) }}</td>
            <td style="font-weight: bold;text-align: right;border: 1px solid #000000;background-color: #F2F2F2;">{{ formatNumber($totalPbbkb) }}</td>
        </tr>
    @endforeach

    @foreach ($allBbmTypes as $jenisBbmId => $bbmData)
    <tr>
        @if ($loop->first)
            <td rowspan="{{ count($allBbmTypes)+1 }}" style="text-align:center;vertical-align: middle;border: 1px solid #000000;">{{ count($data)+1 }}</td>
            <td rowspan="{{ count($allBbmTypes)+1 }}" style="text-align:center;vertical-align: middle; font-weight: bold;border: 1px solid #000000;">Total Semua Perusahaan</td>
        @endif
        <td style="border: 1px solid #000000;">{{ $bbmData['nama_jenis_bbm'] }}</td>
        <td style="text-align: right;border: 1px solid #000000;">{{ formatNumber($bbmData['total_volume_pembelian']) }}</td>
        <td style="text-align: right;border: 1px solid #000000;">{{ formatNumber($bbmData['total_volume_penjualan']) }}</td>
        <td style="text-align: right;border: 1px solid #000000;">{{ formatNumber($bbmData['total_dpp_penjualan']) }}</td>
        <td style="text-align: right;border: 1px solid #000000;">{{ formatNumber($bbmData['total_pbbkb_penjualan']) }}</td>

        @if ($loop->first)
            <td rowspan="{{ count($allBbmTypes)+1 }}" style="text-align: center;font-weight:bold;vertical-align: middle;border: 1px solid #000000;">{{ formatNumber($grandTotalSanksi) }}</td>
            <td rowspan="{{ count($allBbmTypes)+1 }}" style="text-align: center;font-weight:bold;vertical-align: middle;border: 1px solid #000000;">{{ formatNumber($grandTotalSubtotal) }}</td>
        @endif
    </tr>
    @endforeach

    <!-- Final Grand Total Row -->
    <tr>
        <td style="font-weight: bold; text-align: right; background-color: #D9D9D9;border: 1px solid #000000;">GRAND TOTAL</td>
        <td style="font-weight: bold; background-color: #D9D9D9;text-align: right;border: 1px solid #000000;">{{ formatNumber($grandTotalVolumePembelian) }}</td>
        <td style="font-weight: bold; background-color: #D9D9D9;text-align: right;border: 1px solid #000000;">{{ formatNumber($grandTotalVolumePenjualan) }}</td>
        <td style="font-weight: bold; background-color: #D9D9D9;text-align: right;border: 1px solid #000000;">{{ formatNumber($grandTotalDpp) }}</td>
        <td style="font-weight: bold; background-color: #D9D9D9;text-align: right;border: 1px solid #000000;">{{ formatNumber($grandTotalPbbkb) }}</td>
    </tr>
</table>
