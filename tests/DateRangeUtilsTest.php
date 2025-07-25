<?php

use PHPUnit\Framework\TestCase;
use Ogzhncrt\DateRangeHelper\DateRange;
use Ogzhncrt\DateRangeHelper\DateRangeUtils;

class DateRangeUtilsTest extends TestCase
{
    public function testSortRangesByStart()
    {
        $r1 = DateRange::from('2024-01-10')->to('2024-01-20');
        $r2 = DateRange::from('2024-01-01')->to('2024-01-05');
        $sorted = DateRangeUtils::sortRangesByStart([$r1, $r2]);

        $this->assertSame('2024-01-01', $sorted[0]->getStart()->format('Y-m-d'));
    }

    public function testMergeRanges()
    {
        $a = DateRange::from('2024-01-01')->to('2024-01-10');
        $b = DateRange::from('2024-01-08')->to('2024-01-15');
        $c = DateRange::from('2024-01-20')->to('2024-01-25');

        $merged = DateRangeUtils::mergeRanges([$a, $b, $c]);

        $this->assertCount(2, $merged);
        $this->assertEquals('2024-01-01', $merged[0]->getStart()->format('Y-m-d'));
        $this->assertEquals('2024-01-15', $merged[0]->getEnd()->format('Y-m-d'));
    }
}
