<?php

namespace Ogzhncrt\DateRangeHelper;

class TimezoneConfig
{
    private static ?string $defaultTimezone = null;
    private const ENV_TIMEZONE_KEY = 'DATE_RANGE_HELPER_TIMEZONE';
    private const DEFAULT_TIMEZONE = 'UTC';

    /**
     * Get the configured timezone from environment variable or default to UTC
     * 
     * @return string The timezone identifier
     */
    public static function getTimezone(): string
    {
        if (self::$defaultTimezone === null) {
            self::$defaultTimezone = self::resolveTimezone();
        }
        
        return self::$defaultTimezone;
    }

    /**
     * Set a custom timezone (useful for testing or overriding environment)
     * 
     * @param string $timezone The timezone identifier
     * @return void
     */
    public static function setTimezone(string $timezone): void
    {
        if (!self::isValidTimezone($timezone)) {
            throw new \InvalidArgumentException("Invalid timezone: $timezone");
        }
        
        self::$defaultTimezone = $timezone;
    }

    /**
     * Reset timezone to environment/default value
     * 
     * @return void
     */
    public static function resetTimezone(): void
    {
        self::$defaultTimezone = null;
    }

    /**
     * Create a DateTimeImmutable object with the configured timezone
     * 
     * @param string $dateString The date string to parse
     * @return \DateTimeImmutable
     * @throws \DateMalformedStringException When the date string is invalid
     */
    public static function createDateTime(string $dateString): \DateTimeImmutable
    {
        $timezone = new \DateTimeZone(self::getTimezone());
        
        try {
            $date = new \DateTimeImmutable($dateString, $timezone);
            return $date;
        } catch (\DateMalformedStringException $e) {
            throw new \InvalidArgumentException("Invalid date format: $dateString", 0, $e);
        }
    }

    /**
     * Resolve timezone from environment variable with fallback
     * 
     * @return string The resolved timezone
     */
    private static function resolveTimezone(): string
    {
        $envTimezone = $_ENV[self::ENV_TIMEZONE_KEY] ?? $_SERVER[self::ENV_TIMEZONE_KEY] ?? null;
        
        if ($envTimezone !== null) {
            if (self::isValidTimezone($envTimezone)) {
                return $envTimezone;
            }
            
            // Log warning about invalid timezone in environment
            error_log("Warning: Invalid timezone '{$envTimezone}' in environment variable " . self::ENV_TIMEZONE_KEY . ". Using default timezone.");
        }
        
        return self::DEFAULT_TIMEZONE;
    }

    /**
     * Check if a timezone is valid
     * 
     * @param string $timezone The timezone to validate
     * @return bool True if valid
     */
    public static function isValidTimezone(string $timezone): bool
    {
        return in_array($timezone, \DateTimeZone::listIdentifiers(), true);
    }
} 