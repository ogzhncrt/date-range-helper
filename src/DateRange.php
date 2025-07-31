<?php

namespace Ogzhncrt\DateRangeHelper;

use DateTimeInterface;
use Ogzhncrt\DateRangeHelper\Config\TimezoneConfig;
use Ogzhncrt\DateRangeHelper\Config\BusinessDayConfig;

class DateRange
{
    private DateTimeInterface $start;
    private DateTimeInterface $end;

    private function __construct(DateTimeInterface $start, DateTimeInterface $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public static function createFromObjects(\DateTimeInterface $start, \DateTimeInterface $end): self
    {
        return new self($start, $end);
    }

    /**
     * Create a DateRange starting from a specific date with configured timezone
     * 
     * @param string $start The start date string
     * @return self
     */
    public static function from(string $start): self
    {
        $date = TimezoneConfig::createDateTime($start);
        return new self($date, $date);
    }

    /**
     * Set the end date of the range with configured timezone
     * 
     * @param string $end The end date string
     * @return self
     */
    public function to(string $end): self
    {
        $date = TimezoneConfig::createDateTime($end);
        return new self($this->start, $date);
    }

    /**
     * Checks if a date is within this range (inclusive)
     * 
     * @param DateTimeInterface $date The date to check
     * @return bool True if the date is within the range
     */
    public function contains(DateTimeInterface $date): bool
    {
        return $date >= $this->start && $date <= $this->end;
    }

    public function overlaps(DateRange $other): bool
    {
        return $this->start <= $other->end && $other->start <= $this->end;
    }

    public function shift(int $days): self
    {
        $intervalSpec = ($days >= 0 ? '+' : '') . $days . ' days';

        $newStart = $this->start->modify($intervalSpec);
        $newEnd = $this->end->modify($intervalSpec);

        return new self($newStart, $newEnd);
    }

    public function durationInDays(): int
    {
        return (int) $this->start->diff($this->end)->format('%a') + 1;
    }

    public function getStart(): DateTimeInterface
    {
        return $this->start;
    }

    public function getEnd(): DateTimeInterface
    {
        return $this->end;
    }

    /**
     * Get the timezone of this date range
     * 
     * @return string The timezone identifier
     */
    public function getTimezone(): string
    {
        return $this->start->getTimezone()->getName();
    }

    /**
     * Convert this date range to a different timezone
     * 
     * @param string $timezone The target timezone
     * @return self New DateRange in the specified timezone
     */
    public function toTimezone(string $timezone): self
    {
        if (!TimezoneConfig::isValidTimezone($timezone)) {
            throw new \InvalidArgumentException("Invalid timezone: $timezone");
        }

        $targetTimezone = new \DateTimeZone($timezone);
        $newStart = $this->start->setTimezone($targetTimezone);
        $newEnd = $this->end->setTimezone($targetTimezone);

        return new self($newStart, $newEnd);
    }

    /**
     * Get the current configured timezone
     * 
     * @return string The configured timezone identifier
     */
    public static function getConfiguredTimezone(): string
    {
        return TimezoneConfig::getTimezone();
    }

    /**
     * Get the number of business days in this range
     * 
     * @return int Number of business days
     */
    public function businessDaysInRange(): int
    {
        return BusinessDayConfig::countBusinessDays($this->start, $this->end);
    }

    /**
     * Get the number of non-business days in this range
     * 
     * @return int Number of non-business days
     */
    public function nonBusinessDaysInRange(): int
    {
        return $this->durationInDays() - $this->businessDaysInRange();
    }

    /**
     * Shift the range by a specified number of business days
     * 
     * @param int $businessDays Number of business days to shift (positive or negative)
     * @return self New DateRange shifted by business days
     */
    public function shiftBusinessDays(int $businessDays): self
    {
        if ($businessDays === 0) {
            return $this;
        }

        $newStart = $this->start;
        $newEnd = $this->end;

        if ($businessDays > 0) {
            // Shift forward
            for ($i = 0; $i < $businessDays; $i++) {
                $newStart = BusinessDayConfig::getNextBusinessDay($newStart);
                $newEnd = BusinessDayConfig::getNextBusinessDay($newEnd);
            }
        } else {
            // Shift backward
            for ($i = 0; $i < abs($businessDays); $i++) {
                $newStart = BusinessDayConfig::getPreviousBusinessDay($newStart);
                $newEnd = BusinessDayConfig::getPreviousBusinessDay($newEnd);
            }
        }

        return new self($newStart, $newEnd);
    }

    /**
     * Expand the range to include only business days
     * 
     * @return self New DateRange expanded to business days only
     */
    public function expandToBusinessDays(): self
    {
        $newStart = $this->start;
        $newEnd = $this->end;

        // Adjust start to next business day if it's not a business day
        while (!BusinessDayConfig::isBusinessDay($newStart)) {
            $newStart = BusinessDayConfig::getNextBusinessDay($newStart);
        }

        // Adjust end to previous business day if it's not a business day
        while (!BusinessDayConfig::isBusinessDay($newEnd)) {
            $newEnd = BusinessDayConfig::getPreviousBusinessDay($newEnd);
        }

        return new self($newStart, $newEnd);
    }

    /**
     * Get business day ranges within this range
     * 
     * @return array Array of DateRange objects representing business day periods
     */
    public function getBusinessDayRanges(): array
    {
        $ranges = [];
        $current = $this->start;
        $startOfBusinessPeriod = null;

        while ($current <= $this->end) {
            if (BusinessDayConfig::isBusinessDay($current)) {
                if ($startOfBusinessPeriod === null) {
                    $startOfBusinessPeriod = $current;
                }
            } else {
                if ($startOfBusinessPeriod !== null) {
                    $ranges[] = new self($startOfBusinessPeriod, $current->modify('-1 day'));
                    $startOfBusinessPeriod = null;
                }
            }
            $current = $current->modify('+1 day');
        }

        // Handle case where range ends on a business day
        if ($startOfBusinessPeriod !== null) {
            $ranges[] = new self($startOfBusinessPeriod, $this->end);
        }

        return $ranges;
    }

    /**
     * Check if the entire range consists of business days only
     * 
     * @return bool True if all days in range are business days
     */
    public function isBusinessDaysOnly(): bool
    {
        $current = $this->start;
        
        while ($current <= $this->end) {
            if (!BusinessDayConfig::isBusinessDay($current)) {
                return false;
            }
            $current = $current->modify('+1 day');
        }
        
        return true;
    }
}
