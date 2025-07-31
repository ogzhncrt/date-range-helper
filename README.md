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
