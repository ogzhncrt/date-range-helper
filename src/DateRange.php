<?php

namespace Ogzhncrt\DateRangeHelper;

use DateTimeInterface;
use Ogzhncrt\DateRangeHelper\TimezoneConfig;

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
}
