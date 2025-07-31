# 📅 Date Range Helper

A lightweight PHP library for working with date ranges in an elegant and immutable way.  
Includes powerful utilities for comparing, shifting, merging, and analyzing ranges.

---

## ✅ Installation

```bash
composer require ogzhncrt/date-range-helper
```

---

## 🧠 Features

- Immutable `DateRange` class
- Range comparison (`contains`, `overlaps`)
- Range math (`shift`, `durationInDays`)
- Utility class `DateRangeUtils` for sorting & merging ranges
- **Timezone support** with environment variable configuration
- **Business day calculations** with holiday and weekend support

---

## 🚀 Usage

### ✨ Basic Creation

```php
use Ogzhncrt\DateRangeHelper\DateRange;

$range = DateRange::from('2024-01-01')->to('2024-01-10');
```

---

### 🔍 Check if a Date is Within the Range

```php
$range->contains(new DateTime('2024-01-05')); // true
$range->contains(new DateTime('2024-02-01')); // false
```

---

### 🔁 Shift Range Forward or Backward

```php
$shifted = $range->shift(3);   // Jan 4 – Jan 13
$backward = $range->shift(-2); // Dec 30 – Jan 8
```

---

### 📏 Get Range Duration (in days)

```php
$range->durationInDays(); // 10 (inclusive)
```

---

### 🔗 Check if Two Ranges Overlap

```php
$other = DateRange::from('2024-01-08')->to('2024-01-15');
$range->overlaps($other); // true
```

---

### 🌍 Timezone Support

The library supports timezone configuration via environment variable `DATE_RANGE_HELPER_TIMEZONE`:

```bash
# Set timezone in your environment
export DATE_RANGE_HELPER_TIMEZONE="America/New_York"
```

```php
// All date ranges will use the configured timezone
$range = DateRange::from('2024-01-01')->to('2024-01-10');
echo $range->getTimezone(); // "America/New_York"

// Convert to different timezone
$utcRange = $range->toTimezone('UTC');

// Get current configured timezone
echo DateRange::getConfiguredTimezone(); // "America/New_York"
```

---

### 💼 Business Day Calculations

The library supports business day calculations with configurable weekends and holidays:

```php
use Ogzhncrt\DateRangeHelper\Config\BusinessDayConfig;

// Configure weekends (Friday and Saturday)
BusinessDayConfig::setWeekendDays([5, 6]);

// Add holidays
BusinessDayConfig::addHoliday('2024-01-01');
BusinessDayConfig::addHolidays(['2024-12-25', '2024-12-26']);

// Load predefined holiday calendar
BusinessDayConfig::loadHolidayCalendar('US'); // US, EU, TR available

// Load holidays from API (recommended)
BusinessDayConfig::loadHolidaysFromAPI('US', 2024); // Any country, any year

// Business day operations (automatic holiday loading)
$range = DateRange::from('2024-01-01')->to('2024-01-07');
echo $range->businessDaysInRange(); // Automatically loads holidays for the range
echo $range->businessDaysInRange('US'); // Specify country for holidays

$shifted = $range->shiftBusinessDays(2, 'US'); // Shift by 2 business days
$expanded = $range->expandToBusinessDays('US'); // Expand to business days only

// Get business day periods
$businessRanges = $range->getBusinessDayRanges('US'); // Array of business day periods

// Multi-year ranges automatically load holidays for all years
$longRange = DateRange::from('2023-01-01')->to('2024-12-31');
echo $longRange->businessDaysInRange('US'); // Loads holidays for 2023 and 2024
```

---

### 🌍 Holiday API Integration

The library supports dynamic holiday data via external APIs:

```php
use Ogzhncrt\DateRangeHelper\Config\HolidayAPI;

// Configure API (optional)
HolidayAPI::setPreferredAPI('nager'); // Free API, no key required
HolidayAPI::setApiKey('your-key'); // For Calendarific API

// Get holidays for any country
$holidays = HolidayAPI::getHolidays('US', 2024);
$holidays = HolidayAPI::getHolidays('FR', 2024); // France
$holidays = HolidayAPI::getHolidays('DE', 2024); // Germany

// Supported APIs:
// - Nager.Date API: 90+ countries, free, no API key
// - Calendarific API: 230+ countries, requires API key
```

---

### 🔄 Automatic Holiday Loading

The library automatically loads holidays for date ranges:

```php
// Environment variable for default country
export DATE_RANGE_HELPER_COUNTRY="US"

// Automatic holiday loading for any range
$range = DateRange::from('2024-01-01')->to('2024-01-31');
echo $range->businessDaysInRange(); // Uses default country (US)
echo $range->businessDaysInRange('FR'); // Uses specific country

// Multi-year ranges automatically load holidays for all years
$longRange = DateRange::from('2022-01-01')->to('2024-12-31');
echo $longRange->businessDaysInRange('US'); // Loads holidays for 2022, 2023, 2024
```

---

## 🧰 DateRangeUtils

### 📚 Sort Ranges by Start Date

```php
use Ogzhncrt\DateRangeHelper\DateRangeUtils;

$r1 = DateRange::from('2024-01-10')->to('2024-01-20');
$r2 = DateRange::from('2024-01-01')->to('2024-01-05');

$sorted = DateRangeUtils::sortRangesByStart([$r1, $r2]);
// Result: [$r2, $r1]
```

---

### 🧪 Merge Overlapping or Adjacent Ranges

```php
$a = DateRange::from('2024-01-01')->to('2024-01-10');
$b = DateRange::from('2024-01-08')->to('2024-01-15');
$c = DateRange::from('2024-01-20')->to('2024-01-25');

$merged = DateRangeUtils::mergeRanges([$a, $b, $c]);
// Result: [DateRange('2024-01-01', '2024-01-15'), DateRange('2024-01-20', '2024-01-25')]
```

---

## 🧪 Testing

```bash
./vendor/bin/phpunit
```

---

## 📄 License

MIT
