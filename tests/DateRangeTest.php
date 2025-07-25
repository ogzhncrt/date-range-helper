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

    public function testRangesOverlapWhenTheyIntersect()
    {
        $a = DateRange::from('2024-01-01')->to('2024-01-10');
        $b = DateRange::from('2024-01-05')->to('2024-01-15');
        $this->assertTrue($a->overlaps($b));
        $this->assertTrue($b->overlaps($a));
    }

    public function testRangesDoNotOverlapWhenSeparate()
    {
        $a = DateRange::from('2024-01-01')->to('2024-01-10');
        $b = DateRange::from('2024-01-11')->to('2024-01-20');
        $this->assertFalse($a->overlaps($b));
        $this->assertFalse($b->overlaps($a));
    }

    public function testRangesOverlapOnEdge()
    {
        $a = DateRange::from('2024-01-01')->to('2024-01-10');
        $b = DateRange::from('2024-01-10')->to('2024-01-20');
        $this->assertTrue($a->overlaps($b));
        $this->assertTrue($b->overlaps($a));
    }

    public function testShiftForward()
    {
        $range = DateRange::from('2024-01-01')->to('2024-01-10');
        $shifted = $range->shift(3);

        $this->assertEquals('2024-01-04', $shifted->getStart()->format('Y-m-d'));
        $this->assertEquals('2024-01-13', $shifted->getEnd()->format('Y-m-d'));
    }

    public function testShiftBackward()
    {
        $range = DateRange::from('2024-01-01')->to('2024-01-10');
        $shifted = $range->shift(-2);

        $this->assertEquals('2023-12-30', $shifted->getStart()->format('Y-m-d'));
        $this->assertEquals('2024-01-08', $shifted->getEnd()->format('Y-m-d'));
    }

    public function testDurationInDaysForMultipleDays()
    {
        $range = DateRange::from('2024-01-01')->to('2024-01-10');
        $this->assertEquals(10, $range->durationInDays());
    }

    public function testDurationInDaysForSingleDay()
    {
        $range = DateRange::from('2024-05-05')->to('2024-05-05');
        $this->assertEquals(1, $range->durationInDays());
    }


}
