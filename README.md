# Date Range Helper 📅

A small PHP utility for working with date ranges in a clean, readable way.

## Install

```bash
composer require ogzhncrt/date-range-helper
```

## Usage
```php
use Ogzhncrt\DateRangeHelper\DateRange;

$range = DateRange::from('2024-01-01')->to('2024-12-31');

$range->contains(new DateTime('2024-06-01')); // true
$range->contains(new DateTime('2025-01-01')); // false

```

## Test
```bash
./vendor/bin/phpunit
```