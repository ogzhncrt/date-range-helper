<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Ogzhncrt\DateRangeHelper\DateRange;
use Ogzhncrt\DateRangeHelper\Config\BusinessDayConfig;
use Ogzhncrt\DateRangeHelper\Config\HolidayAPI;

echo "=== Date Range Helper Holiday API Example ===\n\n";

// Example 1: Using local fallback holidays
echo "1. Local fallback holidays:\n";
$holidays = HolidayAPI::getHolidays('US', 2024);
echo "   US holidays (2024): " . count($holidays) . " holidays\n";
echo "   Sample: " . implode(', ', array_slice($holidays, 0, 3)) . "...\n\n";

// Example 2: Different countries
echo "2. Different countries:\n";
$countries = ['US', 'FR', 'DE', 'GB', 'TR'];
foreach ($countries as $country) {
    $holidays = HolidayAPI::getHolidays($country, 2024);
    echo "   {$country}: " . count($holidays) . " holidays\n";
}
echo "\n";

// Example 3: Using API with BusinessDayConfig
echo "3. Using API with BusinessDayConfig:\n";
BusinessDayConfig::loadHolidaysFromAPI('US', 2024);
$range = DateRange::from('2024-01-01')->to('2024-01-31');
echo "   Range: {$range->getStart()->format('Y-m-d')} to {$range->getEnd()->format('Y-m-d')}\n";
echo "   Business days: {$range->businessDaysInRange()}\n";
echo "   Non-business days: {$range->nonBusinessDaysInRange()}\n\n";

// Example 4: API configuration
echo "4. API configuration:\n";
HolidayAPI::setPreferredAPI('nager'); // Use Nager.Date API (free)
echo "   Preferred API set to: nager (free, no API key required)\n";

// Example 5: Supported countries
echo "5. Supported countries:\n";
$supportedCountries = HolidayAPI::getSupportedCountries();
echo "   Local fallback: " . implode(', ', $supportedCountries) . "\n";
echo "   API coverage: 90+ countries via Nager.Date API\n";
echo "   API coverage: 230+ countries via Calendarific API\n\n";

// Example 6: Cache functionality
echo "6. Cache functionality:\n";
$start = microtime(true);
$holidays1 = HolidayAPI::getHolidays('US', 2024);
$time1 = microtime(true) - $start;

$start = microtime(true);
$holidays2 = HolidayAPI::getHolidays('US', 2024);
$time2 = microtime(true) - $start;

echo "   First call: " . number_format($time1 * 1000, 2) . "ms\n";
echo "   Cached call: " . number_format($time2 * 1000, 2) . "ms\n";
echo "   Cache hit: " . ($time2 < $time1 ? 'Yes' : 'No') . "\n\n";

// Example 7: Error handling
echo "7. Error handling:\n";
try {
    $holidays = HolidayAPI::getHolidays('XX', 2024); // Unsupported country
    echo "   Unsupported country: " . count($holidays) . " holidays (empty)\n";
} catch (\Exception $e) {
    echo "   Error: " . $e->getMessage() . "\n";
}

try {
    HolidayAPI::setPreferredAPI('invalid');
} catch (\Exception $e) {
    echo "   Invalid API error: " . $e->getMessage() . "\n";
}
echo "\n";

// Example 8: Business day calculations with API holidays
echo "8. Business day calculations with API holidays:\n";
BusinessDayConfig::clearHolidays(); // Clear previous holidays
BusinessDayConfig::loadHolidaysFromAPI('FR', 2024); // Load French holidays

$range = DateRange::from('2024-07-01')->to('2024-07-31'); // July 2024
echo "   Range: {$range->getStart()->format('Y-m-d')} to {$range->getEnd()->format('Y-m-d')}\n";
echo "   Total days: {$range->durationInDays()}\n";
echo "   Business days: {$range->businessDaysInRange()}\n";
echo "   Non-business days: {$range->nonBusinessDaysInRange()}\n";
echo "   French holidays included: Bastille Day (July 14)\n\n";

echo "=== End of Holiday API Example ===\n"; 