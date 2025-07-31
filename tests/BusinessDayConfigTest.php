<?php

use PHPUnit\Framework\TestCase;
use Ogzhncrt\DateRangeHelper\Config\BusinessDayConfig;

class BusinessDayConfigTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Reset configuration before each test
        BusinessDayConfig::reset();
    }

    protected function tearDown(): void
    {
        // Reset configuration after each test
        BusinessDayConfig::reset();
        parent::tearDown();
    }

    public function testDefaultWeekendDays()
    {
        $weekendDays = BusinessDayConfig::getWeekendDays();
        $this->assertEquals([6, 7], $weekendDays); // Saturday and Sunday
    }

    public function testSetWeekendDays()
    {
        BusinessDayConfig::setWeekendDays([5, 6]); // Friday and Saturday
        $weekendDays = BusinessDayConfig::getWeekendDays();
        $this->assertEquals([5, 6], $weekendDays);
    }

    public function testSetWeekendDaysWithInvalidDayThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid day number: 8. Must be 1-7');
        
        BusinessDayConfig::setWeekendDays([1, 8]);
    }

    public function testAddHoliday()
    {
        BusinessDayConfig::addHoliday('2024-01-01');
        $holidays = BusinessDayConfig::getHolidays();
        $this->assertContains('2024-01-01', $holidays);
    }

    public function testAddHolidayWithInvalidFormatThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid holiday date format: invalid-date. Use Y-m-d format');
        
        BusinessDayConfig::addHoliday('invalid-date');
    }

    public function testAddHolidays()
    {
        BusinessDayConfig::addHolidays(['2024-01-01', '2024-12-25']);
        $holidays = BusinessDayConfig::getHolidays();
        $this->assertContains('2024-01-01', $holidays);
        $this->assertContains('2024-12-25', $holidays);
    }

    public function testRemoveHoliday()
    {
        BusinessDayConfig::addHoliday('2024-01-01');
        BusinessDayConfig::removeHoliday('2024-01-01');
        $holidays = BusinessDayConfig::getHolidays();
        $this->assertNotContains('2024-01-01', $holidays);
    }

    public function testClearHolidays()
    {
        BusinessDayConfig::addHolidays(['2024-01-01', '2024-12-25']);
        BusinessDayConfig::clearHolidays();
        $holidays = BusinessDayConfig::getHolidays();
        $this->assertEmpty($holidays);
    }

    public function testIsBusinessDayOnWeekday()
    {
        $date = new \DateTimeImmutable('2024-01-02'); // Tuesday
        $this->assertTrue(BusinessDayConfig::isBusinessDay($date));
    }

    public function testIsBusinessDayOnWeekend()
    {
        $date = new \DateTimeImmutable('2024-01-06'); // Saturday
        $this->assertFalse(BusinessDayConfig::isBusinessDay($date));
    }

    public function testIsBusinessDayOnHoliday()
    {
        BusinessDayConfig::addHoliday('2024-01-02');
        $date = new \DateTimeImmutable('2024-01-02'); // Tuesday
        $this->assertFalse(BusinessDayConfig::isBusinessDay($date));
    }

    public function testGetNextBusinessDay()
    {
        $date = new \DateTimeImmutable('2024-01-06'); // Saturday
        $nextBusinessDay = BusinessDayConfig::getNextBusinessDay($date);
        $this->assertEquals('2024-01-08', $nextBusinessDay->format('Y-m-d')); // Monday
    }

    public function testGetPreviousBusinessDay()
    {
        $date = new \DateTimeImmutable('2024-01-07'); // Sunday
        $previousBusinessDay = BusinessDayConfig::getPreviousBusinessDay($date);
        $this->assertEquals('2024-01-05', $previousBusinessDay->format('Y-m-d')); // Friday
    }

    public function testCountBusinessDays()
    {
        $start = new \DateTimeImmutable('2024-01-01'); // Monday
        $end = new \DateTimeImmutable('2024-01-07'); // Sunday
        $count = BusinessDayConfig::countBusinessDays($start, $end);
        $this->assertEquals(5, $count); // Monday to Friday
    }

    public function testCountBusinessDaysWithHoliday()
    {
        BusinessDayConfig::addHoliday('2024-01-02'); // Tuesday
        $start = new \DateTimeImmutable('2024-01-01'); // Monday
        $end = new \DateTimeImmutable('2024-01-05'); // Friday
        $count = BusinessDayConfig::countBusinessDays($start, $end);
        $this->assertEquals(4, $count); // Monday, Wednesday, Thursday, Friday
    }

    public function testLoadHolidayCalendar()
    {
        BusinessDayConfig::loadHolidayCalendar('US');
        $holidays = BusinessDayConfig::getHolidays();
        $this->assertNotEmpty($holidays);
        $this->assertContains('2024-01-01', $holidays); // New Year's Day
        $this->assertContains('2024-12-25', $holidays); // Christmas Day
    }

    public function testLoadHolidayCalendarWithInvalidCalendar()
    {
        BusinessDayConfig::loadHolidayCalendar('INVALID');
        $holidays = BusinessDayConfig::getHolidays();
        $this->assertEmpty($holidays);
    }

    public function testLoadHolidaysFromAPI()
    {
        BusinessDayConfig::loadHolidaysFromAPI('US', 2024);
        $holidays = BusinessDayConfig::getHolidays();
        $this->assertNotEmpty($holidays);
        $this->assertContains('2024-01-01', $holidays); // New Year's Day
    }

    public function testCustomWeekendConfiguration()
    {
        // Set Friday and Saturday as weekends (common in some countries)
        BusinessDayConfig::setWeekendDays([5, 6]);
        
        $friday = new \DateTimeImmutable('2024-01-05'); // Friday
        $saturday = new \DateTimeImmutable('2024-01-06'); // Saturday
        $sunday = new \DateTimeImmutable('2024-01-07'); // Sunday
        
        $this->assertFalse(BusinessDayConfig::isBusinessDay($friday));
        $this->assertFalse(BusinessDayConfig::isBusinessDay($saturday));
        $this->assertTrue(BusinessDayConfig::isBusinessDay($sunday));
    }

    public function testGetDefaultCountry()
    {
        $defaultCountry = BusinessDayConfig::getDefaultCountry();
        $this->assertEquals('US', $defaultCountry);
    }
} 