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

    public function getStart(): DateTimeInterface
    {
        return $this->start;
    }

    public function getEnd(): DateTimeInterface
    {
        return $this->end;
    }
}
