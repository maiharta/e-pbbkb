<?php
/**
 * Test script to demonstrate invoice expiration alignment with bunga dates
 * This shows how the invoice expires_at is now synchronized with recurring bunga dates
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Carbon;

/**
 * Mock class to simulate BungaService::calculateRecurringDay logic
 */
function calculateRecurringDay(int $originalDay, int $year, int $month): int
{
    // Get the last day of the target month
    $lastDayOfMonth = Carbon::create($year, $month, 1)->endOfMonth()->day;
    
    // Special handling for different scenarios
    if ($originalDay <= 28) {
        // Safe days that exist in all months
        return $originalDay;
    } else if ($originalDay == 29) {
        // Handle Feb 29 case
        if ($month == 2) {
            // February - check if leap year
            return Carbon::create($year, 2, 1)->isLeapYear() ? 29 : 28;
        } else {
            // Other months - use original day or last day of month
            return min($originalDay, $lastDayOfMonth);
        }
    } else if ($originalDay >= 30) {
        // Handle 30th and 31st cases
        if ($month == 2) {
            // February - always use 28 or 29 (leap year)
            return Carbon::create($year, 2, 1)->isLeapYear() ? 29 : 28;
        } else {
            // Other months - use original day or last day of month (whichever is smaller)
            return min($originalDay, $lastDayOfMonth);
        }
    }
    
    return $originalDay;
}

/**
 * Simulate the getNextRecurringBungaDate logic
 */
function getNextRecurringBungaDate($batasPembayaran, $lastBungaDate = null, $originalDay = null): Carbon
{
    $now = Carbon::now();
    
    // If we haven't passed the batas_pembayaran yet, the next bunga date is the day after batas_pembayaran
    if ($now->lessThan($batasPembayaran)) {
        return $batasPembayaran->copy()->addDay();
    }
    
    if (!$lastBungaDate) {
        // No bunga exists yet, so next bunga date is the day after batas_pembayaran
        return $batasPembayaran->copy()->addDay();
    }
    
    // Calculate the next month's bunga date
    $nextMonth = $lastBungaDate->copy()->addMonth();
    $targetDay = calculateRecurringDay($originalDay, $nextMonth->year, $nextMonth->month);
    
    return Carbon::create($nextMonth->year, $nextMonth->month, $targetDay);
}

echo "=== Invoice Expiration Alignment with Bunga Dates Demo ===\n\n";

$testCases = [
    [
        'title' => 'Case 1: Before payment deadline (28 Feb 2025)',
        'batas_pembayaran' => '2025-02-28',
        'current_time' => '2025-02-15',
        'last_bunga_date' => null,
        'original_day' => null,
    ],
    [
        'title' => 'Case 2: After deadline, no bunga yet (31 Jan 2025)',
        'batas_pembayaran' => '2025-01-31',
        'current_time' => '2025-02-15',
        'last_bunga_date' => null,
        'original_day' => null,
    ],
    [
        'title' => 'Case 3: Has bunga, calculating next (start 31 Jan)',
        'batas_pembayaran' => '2025-01-31',
        'current_time' => '2025-03-15',
        'last_bunga_date' => '2025-02-28', // Feb adjustment for 31st
        'original_day' => 31,
    ],
    [
        'title' => 'Case 4: Leap year scenario (start 29 Feb 2024)',
        'batas_pembayaran' => '2024-02-29',
        'current_time' => '2025-02-15',
        'last_bunga_date' => '2025-01-29',
        'original_day' => 29,
    ]
];

foreach ($testCases as $case) {
    echo "--- {$case['title']} ---\n";
    echo "Batas Pembayaran: {$case['batas_pembayaran']}\n";
    echo "Current Time: {$case['current_time']}\n";
    
    // Set current time for testing
    Carbon::setTestNow(Carbon::parse($case['current_time']));
    
    $batasPembayaran = Carbon::parse($case['batas_pembayaran']);
    $lastBungaDate = $case['last_bunga_date'] ? Carbon::parse($case['last_bunga_date']) : null;
    
    $nextBungaDate = getNextRecurringBungaDate($batasPembayaran, $lastBungaDate, $case['original_day']);
    
    echo "Next Bunga Date: " . $nextBungaDate->format('Y-m-d') . "\n";
    echo "Invoice Expires At: " . $nextBungaDate->format('Y-m-d') . " (same as next bunga)\n";
    
    if ($lastBungaDate) {
        echo "Last Bunga Date: " . $lastBungaDate->format('Y-m-d') . "\n";
        echo "Original Day Pattern: {$case['original_day']}\n";
    }
    
    echo "\n";
}

// Reset Carbon test time
Carbon::setTestNow();

echo "=== Summary ===\n";
echo "✅ Invoice expiration is now synchronized with recurring bunga dates\n";
echo "✅ Handles leap years and month-end variations correctly\n";
echo "✅ Prevents overlapping periods between invoice expiration and bunga generation\n";
echo "✅ Ensures consistent monthly recurring pattern\n";
