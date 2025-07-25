<?php

namespace Ogzhncrt\DateRangeHelper;

use DateTimeInterface;

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

    public static function from(string $start): self
    {
        return new self(new \DateTimeImmutable($start), new \DateTimeImmutable($start));
    }

    public function to(string $end): self
    {
        return new self($this->start, new \DateTimeImmutable($end));
    }

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
}
