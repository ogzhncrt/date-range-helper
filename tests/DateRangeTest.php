<?php

use PHPUnit\Framework\TestCase;
use Ogzhncrt\DateRangeHelper\DateRange;
use Ogzhncrt\DateRangeHelper\Config\TimezoneConfig;
use Ogzhncrt\DateRangeHelper\Config\BusinessDayConfig;

class DateRangeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Reset timezone to default before each test
        TimezoneConfig::resetTimezone();
        // Reset business day configuration
        BusinessDayConfig::reset();
    }

    protected function tearDown(): void
    {
        // Reset timezone after each test
        TimezoneConfig::resetTimezone();
        // Reset business day configuration
        BusinessDayConfig::reset();
        parent::tearDown();
    }
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

    public function testDateRangeUsesConfiguredTimezone()
    {
        TimezoneConfig::setTimezone('America/New_York');
        $range = DateRange::from('2024-01-01')->to('2024-01-10');
        
        $this->assertEquals('America/New_York', $range->getTimezone());
    }

    public function testGetConfiguredTimezone()
    {
        TimezoneConfig::setTimezone('Europe/London');
        $this->assertEquals('Europe/London', DateRange::getConfiguredTimezone());
    }

    public function testToTimezoneConvertsToDifferentTimezone()
    {
        TimezoneConfig::setTimezone('UTC');
        $range = DateRange::from('2024-01-01 12:00:00')->to('2024-01-10 12:00:00');
        
        $converted = $range->toTimezone('America/New_York');
        $this->assertEquals('America/New_York', $converted->getTimezone());
        
        // The actual time should be different due to timezone conversion
        $this->assertNotEquals(
            $range->getStart()->format('H:i:s'),
            $converted->getStart()->format('H:i:s')
        );
    }

    public function testToTimezoneWithInvalidTimezoneThrowsException()
    {
        $range = DateRange::from('2024-01-01')->to('2024-01-10');
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid timezone: Invalid/Timezone');
        
        $range->toTimezone('Invalid/Timezone');
    }

    public function testDateRangeWithDifferentTimezones()
    {
        TimezoneConfig::setTimezone('UTC');
        $utcRange = DateRange::from('2024-01-01')->to('2024-01-10');
        
        TimezoneConfig::setTimezone('America/New_York');
        $nyRange = DateRange::from('2024-01-01')->to('2024-01-10');
        
        // Both ranges should represent the same date range but in different timezones
        $this->assertEquals('UTC', $utcRange->getTimezone());
        $this->assertEquals('America/New_York', $nyRange->getTimezone());
    }

    public function testBusinessDaysInRange()
    {
        $range = DateRange::from('2024-01-01')->to('2024-01-07'); // Monday to Sunday
        $this->assertEquals(5, $range->businessDaysInRange()); // Monday to Friday
    }

    public function testBusinessDaysInRangeWithHoliday()
    {
        BusinessDayConfig::addHoliday('2024-01-02'); // Tuesday
        $range = DateRange::from('2024-01-01')->to('2024-01-05'); // Monday to Friday
        $this->assertEquals(4, $range->businessDaysInRange()); // Monday, Wednesday, Thursday, Friday
    }

    public function testNonBusinessDaysInRange()
    {
        $range = DateRange::from('2024-01-01')->to('2024-01-07'); // Monday to Sunday
        $this->assertEquals(2, $range->nonBusinessDaysInRange()); // Saturday and Sunday
    }

    public function testShiftBusinessDaysForward()
    {
        $range = DateRange::from('2024-01-01')->to('2024-01-03'); // Monday to Wednesday
        $shifted = $range->shiftBusinessDays(2);
        
        $this->assertEquals('2024-01-03', $shifted->getStart()->format('Y-m-d')); // Wednesday
        $this->assertEquals('2024-01-05', $shifted->getEnd()->format('Y-m-d')); // Friday
    }

    public function testShiftBusinessDaysBackward()
    {
        $range = DateRange::from('2024-01-03')->to('2024-01-05'); // Wednesday to Friday
        $shifted = $range->shiftBusinessDays(-2);
        
        $this->assertEquals('2024-01-01', $shifted->getStart()->format('Y-m-d')); // Monday
        $this->assertEquals('2024-01-03', $shifted->getEnd()->format('Y-m-d')); // Wednesday
    }

    public function testExpandToBusinessDays()
    {
        $range = DateRange::from('2024-01-06')->to('2024-01-07'); // Saturday to Sunday
        $expanded = $range->expandToBusinessDays();
        
        $this->assertEquals('2024-01-08', $expanded->getStart()->format('Y-m-d')); // Monday
        $this->assertEquals('2024-01-05', $expanded->getEnd()->format('Y-m-d')); // Friday
    }

    public function testGetBusinessDayRanges()
    {
        $range = DateRange::from('2024-01-01')->to('2024-01-07'); // Monday to Sunday
        $businessRanges = $range->getBusinessDayRanges();
        
        $this->assertCount(1, $businessRanges);
        $this->assertEquals('2024-01-01', $businessRanges[0]->getStart()->format('Y-m-d')); // Monday
        $this->assertEquals('2024-01-05', $businessRanges[0]->getEnd()->format('Y-m-d')); // Friday
    }

    public function testGetBusinessDayRangesWithGaps()
    {
        BusinessDayConfig::addHoliday('2024-01-03'); // Wednesday
        $range = DateRange::from('2024-01-01')->to('2024-01-07'); // Monday to Sunday
        $businessRanges = $range->getBusinessDayRanges();
        
        $this->assertCount(2, $businessRanges);
        $this->assertEquals('2024-01-01', $businessRanges[0]->getStart()->format('Y-m-d')); // Monday
        $this->assertEquals('2024-01-02', $businessRanges[0]->getEnd()->format('Y-m-d')); // Tuesday
        $this->assertEquals('2024-01-04', $businessRanges[1]->getStart()->format('Y-m-d')); // Thursday
        $this->assertEquals('2024-01-05', $businessRanges[1]->getEnd()->format('Y-m-d')); // Friday
    }

    public function testIsBusinessDaysOnly()
    {
        $businessRange = DateRange::from('2024-01-01')->to('2024-01-05'); // Monday to Friday
        $this->assertTrue($businessRange->isBusinessDaysOnly());
        
        $mixedRange = DateRange::from('2024-01-01')->to('2024-01-07'); // Monday to Sunday
        $this->assertFalse($mixedRange->isBusinessDaysOnly());
    }
}
