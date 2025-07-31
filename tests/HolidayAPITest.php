<?php

use PHPUnit\Framework\TestCase;
use Ogzhncrt\DateRangeHelper\Config\HolidayAPI;

class HolidayAPITest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Clear cache before each test
        HolidayAPI::clearCache();
    }

    protected function tearDown(): void
    {
        // Clear cache after each test
        HolidayAPI::clearCache();
        parent::tearDown();
    }

    public function testSetApiKey()
    {
        HolidayAPI::setApiKey('test-key');
        // No exception should be thrown
        $this->assertTrue(true);
    }

    public function testSetPreferredAPIWithValidAPI()
    {
        HolidayAPI::setPreferredAPI('nager');
        HolidayAPI::setPreferredAPI('calendarific');
        // No exception should be thrown
        $this->assertTrue(true);
    }

    public function testSetPreferredAPIWithInvalidAPIThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid API: invalid. Use \'nager\' or \'calendarific\'');
        
        HolidayAPI::setPreferredAPI('invalid');
    }

    public function testGetLocalHolidaysForUS()
    {
        $holidays = HolidayAPI::getHolidays('US', 2024);
        $this->assertNotEmpty($holidays);
        $this->assertContains('2024-01-01', $holidays); // New Year's Day
        $this->assertContains('2024-12-25', $holidays); // Christmas Day
    }

    public function testGetLocalHolidaysForFR()
    {
        $holidays = HolidayAPI::getHolidays('FR', 2024);
        $this->assertNotEmpty($holidays);
        $this->assertContains('2024-01-01', $holidays); // New Year's Day
        $this->assertContains('2024-07-14', $holidays); // Bastille Day
    }

    public function testGetLocalHolidaysForUnsupportedCountry()
    {
        $holidays = HolidayAPI::getHolidays('XX', 2024);
        $this->assertEmpty($holidays);
    }

    public function testIsCountrySupported()
    {
        $this->assertTrue(HolidayAPI::isCountrySupported('US'));
        $this->assertTrue(HolidayAPI::isCountrySupported('FR'));
        $this->assertFalse(HolidayAPI::isCountrySupported('XX'));
    }

    public function testGetSupportedCountries()
    {
        $countries = HolidayAPI::getSupportedCountries();
        $this->assertIsArray($countries);
        $this->assertContains('US', $countries);
        $this->assertContains('FR', $countries);
        $this->assertContains('DE', $countries);
        $this->assertContains('GB', $countries);
        $this->assertContains('TR', $countries);
    }

    public function testClearCache()
    {
        // Get holidays to populate cache
        HolidayAPI::getHolidays('US', 2024);
        
        // Clear cache
        HolidayAPI::clearCache();
        
        // Cache should be empty (we can't directly test this, but no exception should be thrown)
        $this->assertTrue(true);
    }

    public function testCacheFunctionality()
    {
        // First call should populate cache
        $holidays1 = HolidayAPI::getHolidays('US', 2024);
        
        // Second call should use cache
        $holidays2 = HolidayAPI::getHolidays('US', 2024);
        
        $this->assertEquals($holidays1, $holidays2);
    }
} 