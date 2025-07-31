<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Ogzhncrt\DateRangeHelper\DateRange;
use Ogzhncrt\DateRangeHelper\Config\BusinessDayConfig;

echo "=== Date Range Helper Business Days Example ===\n\n";

// Example 1: Basic business day counting
echo "1. Basic business day counting:\n";
$range = DateRange::from('2024-01-01')->to('2024-01-07'); // Monday to Sunday
echo "   Range: {$range->getStart()->format('Y-m-d')} to {$range->getEnd()->format('Y-m-d')}\n";
echo "   Total days: {$range->durationInDays()}\n";
echo "   Business days: {$range->businessDaysInRange()}\n";
echo "   Non-business days: {$range->nonBusinessDaysInRange()}\n\n";

// Example 2: Adding holidays
echo "2. Adding holidays:\n";
BusinessDayConfig::addHoliday('2024-01-02'); // Tuesday
BusinessDayConfig::addHoliday('2024-01-03'); // Wednesday
$range = DateRange::from('2024-01-01')->to('2024-01-05'); // Monday to Friday
echo "   Range: {$range->getStart()->format('Y-m-d')} to {$range->getEnd()->format('Y-m-d')}\n";
echo "   Business days (with holidays): {$range->businessDaysInRange()}\n";
echo "   Holidays added: Tuesday and Wednesday\n\n";

// Example 3: Shifting by business days
echo "3. Shifting by business days:\n";
$original = DateRange::from('2024-01-01')->to('2024-01-03'); // Monday to Wednesday
echo "   Original: {$original->getStart()->format('Y-m-d')} to {$original->getEnd()->format('Y-m-d')}\n";

$shiftedForward = $original->shiftBusinessDays(2);
echo "   Shifted +2 business days: {$shiftedForward->getStart()->format('Y-m-d')} to {$shiftedForward->getEnd()->format('Y-m-d')}\n";

$shiftedBackward = $original->shiftBusinessDays(-1);
echo "   Shifted -1 business day: {$shiftedBackward->getStart()->format('Y-m-d')} to {$shiftedBackward->getEnd()->format('Y-m-d')}\n\n";

// Example 4: Expanding to business days
echo "4. Expanding to business days:\n";
$weekendRange = DateRange::from('2024-01-06')->to('2024-01-07'); // Saturday to Sunday
echo "   Weekend range: {$weekendRange->getStart()->format('Y-m-d')} to {$weekendRange->getEnd()->format('Y-m-d')}\n";

$expanded = $weekendRange->expandToBusinessDays();
echo "   Expanded to business days: {$expanded->getStart()->format('Y-m-d')} to {$expanded->getEnd()->format('Y-m-d')}\n\n";

// Example 5: Getting business day ranges
echo "5. Getting business day ranges:\n";
BusinessDayConfig::clearHolidays(); // Clear previous holidays
BusinessDayConfig::addHoliday('2024-01-03'); // Wednesday
$range = DateRange::from('2024-01-01')->to('2024-01-07'); // Monday to Sunday
echo "   Range: {$range->getStart()->format('Y-m-d')} to {$range->getEnd()->format('Y-m-d')}\n";
echo "   Holiday: Wednesday (2024-01-03)\n";

$businessRanges = $range->getBusinessDayRanges();
echo "   Business day ranges: " . count($businessRanges) . " periods\n";
foreach ($businessRanges as $i => $businessRange) {
    echo "     Period " . ($i + 1) . ": {$businessRange->getStart()->format('Y-m-d')} to {$businessRange->getEnd()->format('Y-m-d')}\n";
}
echo "\n";

// Example 6: Custom weekend configuration
echo "6. Custom weekend configuration:\n";
BusinessDayConfig::reset();
BusinessDayConfig::setWeekendDays([5, 6]); // Friday and Saturday
echo "   Weekend days set to: Friday and Saturday\n";

$range = DateRange::from('2024-01-01')->to('2024-01-07'); // Monday to Sunday
echo "   Range: {$range->getStart()->format('Y-m-d')} to {$range->getEnd()->format('Y-m-d')}\n";
echo "   Business days: {$range->businessDaysInRange()}\n";
echo "   Non-business days: {$range->nonBusinessDaysInRange()}\n\n";

// Example 7: Loading holiday calendar
echo "7. Loading holiday calendar:\n";
BusinessDayConfig::reset(); // Reset to default
BusinessDayConfig::loadHolidayCalendar('US');
$holidays = BusinessDayConfig::getHolidays();
echo "   US holidays loaded: " . count($holidays) . " holidays\n";
echo "   Sample holidays: " . implode(', ', array_slice($holidays, 0, 3)) . "...\n\n";

// Example 8: Checking if range is business days only
echo "8. Checking business days only:\n";
$businessRange = DateRange::from('2024-01-01')->to('2024-01-05'); // Monday to Friday
$mixedRange = DateRange::from('2024-01-01')->to('2024-01-07'); // Monday to Sunday

echo "   Monday-Friday is business days only: " . ($businessRange->isBusinessDaysOnly() ? 'Yes' : 'No') . "\n";
echo "   Monday-Sunday is business days only: " . ($mixedRange->isBusinessDaysOnly() ? 'Yes' : 'No') . "\n\n";

// Example 9: Next and previous business days
echo "9. Next and previous business days:\n";
$date = new \DateTimeImmutable('2024-01-06'); // Saturday
$nextBusinessDay = BusinessDayConfig::getNextBusinessDay($date);
$previousBusinessDay = BusinessDayConfig::getPreviousBusinessDay($date);

echo "   Date: {$date->format('Y-m-d')} (Saturday)\n";
echo "   Next business day: {$nextBusinessDay->format('Y-m-d')}\n";
echo "   Previous business day: {$previousBusinessDay->format('Y-m-d')}\n\n";

echo "=== End of Business Days Example ===\n"; 