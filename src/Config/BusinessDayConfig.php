<?php

namespace Ogzhncrt\DateRangeHelper\Config;

class BusinessDayConfig
{
    private static array $weekendDays = [6, 7]; // Saturday (6) and Sunday (7) by default
    private static array $holidays = [];
    private static array $holidayCalendars = [];
    
    private const ENV_WEEKEND_DAYS = 'DATE_RANGE_HELPER_WEEKEND_DAYS';
    private const ENV_HOLIDAYS = 'DATE_RANGE_HELPER_HOLIDAYS';

    /**
     * Set which days of the week are considered weekends
     * 
     * @param array $days Array of day numbers (1=Monday, 7=Sunday)
     * @return void
     */
    public static function setWeekendDays(array $days): void
    {
        foreach ($days as $day) {
            if (!is_numeric($day) || $day < 1 || $day > 7) {
                throw new \InvalidArgumentException("Invalid day number: $day. Must be 1-7");
            }
        }
        self::$weekendDays = array_unique($days);
    }

    /**
     * Get the configured weekend days
     * 
     * @return array Array of weekend day numbers
     */
    public static function getWeekendDays(): array
    {
        return self::$weekendDays;
    }

    /**
     * Add a holiday date
     * 
     * @param string $date Holiday date in Y-m-d format
     * @return void
     */
    public static function addHoliday(string $date): void
    {
        $parsedDate = \DateTimeImmutable::createFromFormat('Y-m-d', $date);
        if ($parsedDate === false) {
            throw new \InvalidArgumentException("Invalid holiday date format: $date. Use Y-m-d format");
        }
        
        self::$holidays[] = $parsedDate->format('Y-m-d');
        self::$holidays = array_unique(self::$holidays);
    }

    /**
     * Add multiple holiday dates
     * 
     * @param array $dates Array of holiday dates in Y-m-d format
     * @return void
     */
    public static function addHolidays(array $dates): void
    {
        foreach ($dates as $date) {
            self::addHoliday($date);
        }
    }

    /**
     * Remove a holiday date
     * 
     * @param string $date Holiday date in Y-m-d format
     * @return void
     */
    public static function removeHoliday(string $date): void
    {
        $key = array_search($date, self::$holidays);
        if ($key !== false) {
            unset(self::$holidays[$key]);
            self::$holidays = array_values(self::$holidays);
        }
    }

    /**
     * Get all configured holidays
     * 
     * @return array Array of holiday dates
     */
    public static function getHolidays(): array
    {
        return self::$holidays;
    }

    /**
     * Clear all holidays
     * 
     * @return void
     */
    public static function clearHolidays(): void
    {
        self::$holidays = [];
    }

    /**
     * Load holidays from a predefined calendar
     * 
     * @param string $calendar Calendar name (e.g., 'US', 'EU', 'TR')
     * @return void
     */
    public static function loadHolidayCalendar(string $calendar): void
    {
        $holidays = self::getPredefinedHolidays($calendar);
        if (!empty($holidays)) {
            self::addHolidays($holidays);
        }
    }

    /**
     * Check if a date is a business day (not weekend and not holiday)
     * 
     * @param \DateTimeInterface $date The date to check
     * @return bool True if it's a business day
     */
    public static function isBusinessDay(\DateTimeInterface $date): bool
    {
        $dayOfWeek = (int) $date->format('N'); // 1=Monday, 7=Sunday
        $dateString = $date->format('Y-m-d');
        
        // Check if it's a weekend
        if (in_array($dayOfWeek, self::$weekendDays)) {
            return false;
        }
        
        // Check if it's a holiday
        if (in_array($dateString, self::$holidays)) {
            return false;
        }
        
        return true;
    }

    /**
     * Get the next business day from a given date
     * 
     * @param \DateTimeInterface $date The starting date
     * @return \DateTimeImmutable The next business day
     */
    public static function getNextBusinessDay(\DateTimeInterface $date): \DateTimeImmutable
    {
        $current = \DateTimeImmutable::createFromInterface($date);
        
        do {
            $current = $current->modify('+1 day');
        } while (!self::isBusinessDay($current));
        
        return $current;
    }

    /**
     * Get the previous business day from a given date
     * 
     * @param \DateTimeInterface $date The starting date
     * @return \DateTimeImmutable The previous business day
     */
    public static function getPreviousBusinessDay(\DateTimeInterface $date): \DateTimeImmutable
    {
        $current = \DateTimeImmutable::createFromInterface($date);
        
        do {
            $current = $current->modify('-1 day');
        } while (!self::isBusinessDay($current));
        
        return $current;
    }

    /**
     * Count business days between two dates
     * 
     * @param \DateTimeInterface $start Start date
     * @param \DateTimeInterface $end End date
     * @return int Number of business days
     */
    public static function countBusinessDays(\DateTimeInterface $start, \DateTimeInterface $end): int
    {
        $count = 0;
        $current = \DateTimeImmutable::createFromInterface($start);
        $endDate = \DateTimeImmutable::createFromInterface($end);
        
        while ($current <= $endDate) {
            if (self::isBusinessDay($current)) {
                $count++;
            }
            $current = $current->modify('+1 day');
        }
        
        return $count;
    }

    /**
     * Get predefined holiday calendars
     * 
     * @param string $calendar Calendar name
     * @return array Array of holiday dates
     */
    private static function getPredefinedHolidays(string $calendar): array
    {
        $calendars = [
            'US' => [
                '2024-01-01', // New Year's Day
                '2024-01-15', // Martin Luther King Jr. Day
                '2024-02-19', // Presidents' Day
                '2024-05-27', // Memorial Day
                '2024-07-04', // Independence Day
                '2024-09-02', // Labor Day
                '2024-10-14', // Columbus Day
                '2024-11-11', // Veterans Day
                '2024-11-28', // Thanksgiving Day
                '2024-12-25', // Christmas Day
            ],
            'EU' => [
                '2024-01-01', // New Year's Day
                '2024-05-01', // Labor Day
                '2024-05-08', // Victory in Europe Day
                '2024-12-25', // Christmas Day
                '2024-12-26', // Boxing Day
            ],
            'TR' => [
                '2024-01-01', // New Year's Day
                '2024-04-10', // Ramadan Feast (approximate)
                '2024-04-11',
                '2024-04-12',
                '2024-04-23', // National Sovereignty and Children's Day
                '2024-05-01', // Labor Day
                '2024-05-19', // Commemoration of Atatürk, Youth and Sports Day
                '2024-06-16', // Sacrifice Feast (approximate)
                '2024-06-17',
                '2024-06-18',
                '2024-06-19',
                '2024-07-15', // Democracy and National Unity Day
                '2024-08-30', // Victory Day
                '2024-10-29', // Republic Day
            ]
        ];
        
        return $calendars[$calendar] ?? [];
    }

    /**
     * Reset configuration to defaults
     * 
     * @return void
     */
    public static function reset(): void
    {
        self::$weekendDays = [6, 7];
        self::$holidays = [];
    }
} 