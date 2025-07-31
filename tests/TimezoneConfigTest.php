<?php

use PHPUnit\Framework\TestCase;
use Ogzhncrt\DateRangeHelper\Config\TimezoneConfig;

class TimezoneConfigTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Reset timezone to default before each test
        TimezoneConfig::resetTimezone();
    }

    protected function tearDown(): void
    {
        // Reset timezone after each test
        TimezoneConfig::resetTimezone();
        parent::tearDown();
    }

    public function testGetTimezoneReturnsDefaultWhenNoEnvironmentVariable()
    {
        $timezone = TimezoneConfig::getTimezone();
        $this->assertEquals('UTC', $timezone);
    }

    public function testSetTimezoneWithValidTimezone()
    {
        TimezoneConfig::setTimezone('America/New_York');
        $this->assertEquals('America/New_York', TimezoneConfig::getTimezone());
    }

    public function testSetTimezoneWithInvalidTimezoneThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid timezone: Invalid/Timezone');
        
        TimezoneConfig::setTimezone('Invalid/Timezone');
    }

    public function testIsValidTimezoneWithValidTimezone()
    {
        $this->assertTrue(TimezoneConfig::isValidTimezone('Europe/London'));
        $this->assertTrue(TimezoneConfig::isValidTimezone('UTC'));
        $this->assertTrue(TimezoneConfig::isValidTimezone('America/New_York'));
    }

    public function testIsValidTimezoneWithInvalidTimezone()
    {
        $this->assertFalse(TimezoneConfig::isValidTimezone('Invalid/Timezone'));
        $this->assertFalse(TimezoneConfig::isValidTimezone(''));
    }

    public function testCreateDateTimeWithValidDate()
    {
        $date = TimezoneConfig::createDateTime('2024-01-01');
        $this->assertInstanceOf(\DateTimeImmutable::class, $date);
        $this->assertEquals('2024-01-01', $date->format('Y-m-d'));
    }

    public function testCreateDateTimeWithInvalidDateThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid date format: invalid-date');
        
        TimezoneConfig::createDateTime('invalid-date');
    }

    public function testCreateDateTimeUsesConfiguredTimezone()
    {
        TimezoneConfig::setTimezone('America/New_York');
        $date = TimezoneConfig::createDateTime('2024-01-01 12:00:00');
        
        $this->assertEquals('America/New_York', $date->getTimezone()->getName());
    }

    public function testResetTimezone()
    {
        TimezoneConfig::setTimezone('Europe/London');
        $this->assertEquals('Europe/London', TimezoneConfig::getTimezone());
        
        TimezoneConfig::resetTimezone();
        $this->assertEquals('UTC', TimezoneConfig::getTimezone());
    }
} 