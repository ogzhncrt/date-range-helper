<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Ogzhncrt\DateRangeHelper\DateRange;
use Ogzhncrt\DateRangeHelper\TimezoneConfig;

echo "=== Date Range Helper Timezone Example ===\n\n";

// Example 1: Using default timezone (UTC)
echo "1. Default timezone (UTC):\n";
$utcRange = DateRange::from('2024-01-01 12:00:00')->to('2024-01-10 12:00:00');
echo "   Range: {$utcRange->getStart()->format('Y-m-d H:i:s T')} to {$utcRange->getEnd()->format('Y-m-d H:i:s T')}\n";
echo "   Timezone: {$utcRange->getTimezone()}\n\n";

// Example 2: Setting timezone programmatically
echo "2. Setting timezone to America/New_York:\n";
TimezoneConfig::setTimezone('America/New_York');
$nyRange = DateRange::from('2024-01-01 12:00:00')->to('2024-01-10 12:00:00');
echo "   Range: {$nyRange->getStart()->format('Y-m-d H:i:s T')} to {$nyRange->getEnd()->format('Y-m-d H:i:s T')}\n";
echo "   Timezone: {$nyRange->getTimezone()}\n\n";

// Example 3: Converting to different timezone
echo "3. Converting to Europe/London:\n";
$londonRange = $nyRange->toTimezone('Europe/London');
echo "   Range: {$londonRange->getStart()->format('Y-m-d H:i:s T')} to {$londonRange->getEnd()->format('Y-m-d H:i:s T')}\n";
echo "   Timezone: {$londonRange->getTimezone()}\n\n";

// Example 4: Environment variable usage
echo "4. Environment variable usage:\n";
echo "   Set DATE_RANGE_HELPER_TIMEZONE environment variable to change default timezone\n";
echo "   Example: export DATE_RANGE_HELPER_TIMEZONE=\"Asia/Tokyo\"\n\n";

// Example 5: Getting configured timezone
echo "5. Current configured timezone:\n";
echo "   " . DateRange::getConfiguredTimezone() . "\n\n";

// Example 6: Timezone validation
echo "6. Timezone validation:\n";
$validTimezones = ['UTC', 'America/New_York', 'Europe/London', 'Asia/Tokyo'];
$invalidTimezones = ['Invalid/Timezone', ''];

foreach ($validTimezones as $tz) {
    $isValid = TimezoneConfig::isValidTimezone($tz);
    echo "   $tz: " . ($isValid ? 'Valid' : 'Invalid') . "\n";
}

foreach ($invalidTimezones as $tz) {
    $isValid = TimezoneConfig::isValidTimezone($tz);
    echo "   $tz: " . ($isValid ? 'Valid' : 'Invalid') . "\n";
}

echo "\n=== End of Example ===\n"; 