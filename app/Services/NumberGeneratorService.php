<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Sptpd;
use App\Models\Pelaporan;

class NumberGeneratorService
{
    /**
     * Generate a formatted SPTPD number
     * Format: II/id_user/Bulan/Tahun/Counter
     *
     * @param Pelaporan $pelaporan Pelaporan instance
     * @return string Formatted SPTPD number
     */
    public static function generateSptpdNumber(Pelaporan $pelaporan)
    {
        if(!$pelaporan->sptpd_approved_at) {
            // If SPTPD is not approved, return null
            throw new ServiceException('SPTPD belum disetujui.');
        }
        if($pelaporan->sptpd_number) {
            // If SPTPD number already exists, return it
            return $pelaporan->sptpd_number;
        }

        // Get the counter for this user/month/year combination
        $counter = self::getSptpdCounter($pelaporan->user_id, $pelaporan->bulan, $pelaporan->tahun);

        // Format the number
        $formattedNumber = sprintf(
            '%s/%s/%02d/%d/%04d',
            'I',
            $pelaporan->user_id,
            $pelaporan->bulan,
            $pelaporan->tahun,
            $counter
        );

        $pelaporan->sptpd_number = $formattedNumber;
        $pelaporan->save();

        return $formattedNumber;
    }

    /**
     * Get the counter for SPTPD numbers for a specific user/month/year
     *
     * @param int $userId User ID
     * @param int $month Month (01-12)
     * @param int $year Year (e.g., 2023)
     * @return int Counter value
     */
    private static function getSptpdCounter($userId, $month, $year)
    {
        // Count existing SPTPD numbers for this user/month/year
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $count = Pelaporan::where('tahun', $year)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('sptpd_number')
            ->count();

        // Return count + 1 for the new number
        return $count + 1;
    }

    /**
     * Generate a formatted SSPD number
     * Format: II/id_user/Bulan/Tahun/Counter
     *
     * @param Pelaporan $pelaporan Pelaporan instance
     * @return string Formatted SSPD number
     */
    public static function generateSspdNumber(Pelaporan $pelaporan)
    {
        if(!$pelaporan->sptpd_approved_at) {
            // If SPTPD is not approved, return null
            throw new ServiceException('SPTPD belum disetujui.');
        }
        if($pelaporan->sspd_number) {
            // If SSPD number already exists, return it
            return $pelaporan->sspd_number;
        }

        // Get the counter for this user/month/year combination
        $counter = self::getSspdCounter($pelaporan->user_id, $pelaporan->bulan, $pelaporan->tahun);

        // Format the number
        $formattedNumber = sprintf(
            '%s/%s/%02d/%d/%04d',
            'II',
            $pelaporan->user_id,
            $pelaporan->bulan,
            $pelaporan->tahun,
            $counter
        );

        $pelaporan->sspd_number = $formattedNumber;
        $pelaporan->save();

        return $formattedNumber;
    }
    /**
     * Get the counter for SSPD numbers for a specific user/month/year
     *
     * @param int $userId User ID
     * @param int $month Month (01-12)
     * @param int $year Year (e.g., 2023)
     * @return int Counter value
     */
    private static function getSspdCounter($userId, $month, $year)
    {
        // Count existing SSPD numbers for this user/month/year
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        $count = Pelaporan::where('tahun', $year)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('sspd_number')
            ->count();

        // Return count + 1 for the new number
        return $count + 1;
    }
}
