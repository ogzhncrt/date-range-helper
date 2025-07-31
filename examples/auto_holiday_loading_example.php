<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Ogzhncrt\DateRangeHelper\DateRange;
use Ogzhncrt\DateRangeHelper\Config\BusinessDayConfig;

echo "=== Date Range Helper Auto Holiday Loading Example ===\n\n";

// Example 1: Automatic holiday loading for single year
echo "1. Single year range (automatic holiday loading):\n";
$range = DateRange::from('2024-01-01')->to('2024-01-31');
echo "   Range: {$range->getStart()->format('Y-m-d')} to {$range->getEnd()->format('Y-m-d')}\n";
echo "   Business days: {$range->businessDaysInRange()}\n";
echo "   Non-business days: {$range->nonBusinessDaysInRange()}\n";
echo "   Note: Holidays automatically loaded for 2024\n\n";

// Example 2: Multi-year range (automatic holiday loading for all years)
echo "2. Multi-year range (automatic holiday loading for all years):\n";
$multiYearRange = DateRange::from('2023-12-25')->to('2024-01-05');
echo "   Range: {$multiYearRange->getStart()->format('Y-m-d')} to {$multiYearRange->getEnd()->format('Y-m-d')}\n";
echo "   Business days: {$multiYearRange->businessDaysInRange()}\n";
echo "   Note: Holidays automatically loaded for 2023 and 2024\n\n";

// Example 3: Specific country code
echo "3. Specific country code:\n";
$range = DateRange::from('2024-01-01')->to('2024-01-31');
echo "   Range: {$range->getStart()->format('Y-m-d')} to {$range->getEnd()->format('Y-m-d')}\n";
echo "   US business days: {$range->businessDaysInRange('US')}\n";
echo "   FR business days: {$range->businessDaysInRange('FR')}\n";
echo "   Note: Different holidays for different countries\n\n";

// Example 4: Environment variable for default country
echo "4. Environment variable for default country:\n";
echo "   Set DATE_RANGE_HELPER_COUNTRY environment variable to change default country\n";
echo "   Example: export DATE_RANGE_HELPER_COUNTRY=\"FR\"\n";
echo "   Current default: " . BusinessDayConfig::getDefaultCountry() . "\n\n";

// Example 5: Business day operations with automatic holiday loading
echo "5. Business day operations with automatic holiday loading:\n";
$range = DateRange::from('2024-01-01')->to('2024-01-10');
echo "   Original range: {$range->getStart()->format('Y-m-d')} to {$range->getEnd()->format('Y-m-d')}\n";

$shifted = $range->shiftBusinessDays(2, 'US');
echo "   Shifted +2 business days: {$shifted->getStart()->format('Y-m-d')} to {$shifted->getEnd()->format('Y-m-d')}\n";

$expanded = $range->expandToBusinessDays('US');
echo "   Expanded to business days: {$expanded->getStart()->format('Y-m-d')} to {$expanded->getEnd()->format('Y-m-d')}\n\n";

// Example 6: Business day ranges with automatic holiday loading
echo "6. Business day ranges with automatic holiday loading:\n";
$range = DateRange::from('2024-01-01')->to('2024-01-07');
$businessRanges = $range->getBusinessDayRanges('US');
echo "   Range: {$range->getStart()->format('Y-m-d')} to {$range->getEnd()->format('Y-m-d')}\n";
echo "   Business day periods: " . count($businessRanges) . "\n";
foreach ($businessRanges as $i => $businessRange) {
    echo "     Period " . ($i + 1) . ": {$businessRange->getStart()->format('Y-m-d')} to {$businessRange->getEnd()->format('Y-m-d')}\n";
}
echo "\n";

// Example 7: Long range spanning multiple years
echo "7. Long range spanning multiple years:\n";
$longRange = DateRange::from('2022-12-01')->to('2024-12-31');
echo "   Range: {$longRange->getStart()->format('Y-m-d')} to {$longRange->getEnd()->format('Y-m-d')}\n";
echo "   Business days: {$longRange->businessDaysInRange('US')}\n";
echo "   Note: Holidays automatically loaded for 2022, 2023, and 2024\n\n";

// Example 8: Performance comparison
echo "8. Performance comparison:\n";
$start = microtime(true);
$businessDays1 = $range->businessDaysInRange('US');
$time1 = microtime(true) - $start;

$start = microtime(true);
$businessDays2 = $range->businessDaysInRange('US');
$time2 = microtime(true) - $start;

echo "   First call (with API): " . number_format($time1 * 1000, 2) . "ms\n";
echo "   Second call (cached): " . number_format($time2 * 1000, 2) . "ms\n";
echo "   Cache benefit: " . number_format(($time1 - $time2) / $time1 * 100, 1) . "% faster\n\n";

echo "=== End of Auto Holiday Loading Example ===\n"; 