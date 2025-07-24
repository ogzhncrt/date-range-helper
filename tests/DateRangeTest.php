<?php

use PHPUnit\Framework\TestCase;
use Ogzhncrt\DateRangeHelper\DateRange;

class DateRangeTest extends TestCase
{
    public function testContainsReturnsTrueWhenDateInsideRange()
    {
        $range = DateRange::from('2024-01-01')->to('2024-12-31');
        $this->assertTrue($range->contains(new DateTime('2024-06-15')));
    }

    public function testContainsReturnsFalseWhenDateBeforeRange()
    {
        $range = DateRange::from('2024-01-01')->to('2024-12-31');
        $this->assertFalse($range->contains(new DateTime('2023-12-31')));
    }

    public function testContainsReturnsFalseWhenDateAfterRange()
    {
        $range = DateRange::from('2024-01-01')->to('2024-12-31');
        $this->assertFalse($range->contains(new DateTime('2025-01-01')));
    }
}
