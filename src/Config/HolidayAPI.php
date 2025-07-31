<?php

namespace Ogzhncrt\DateRangeHelper\Config;

class HolidayAPI
{
    private const CACHE_DURATION = 86400; // 24 hours
    private const NAGER_API_BASE = 'https://date.nager.at/api/v3/PublicHolidays';
    private const CALENDARIFIC_API_BASE = 'https://calendarific.com/api/v2/holidays';
    
    private static array $cache = [];
    private static ?string $apiKey = null;
    private static string $preferredAPI = 'nager'; // 'nager' or 'calendarific'

    /**
     * Set API key for Calendarific (optional)
     * 
     * @param string $apiKey Calendarific API key
     * @return void
     */
    public static function setApiKey(string $apiKey): void
    {
        self::$apiKey = $apiKey;
    }

    /**
     * Set preferred API service
     * 
     * @param string $api 'nager' or 'calendarific'
     * @return void
     */
    public static function setPreferredAPI(string $api): void
    {
        if (!in_array($api, ['nager', 'calendarific'])) {
            throw new \InvalidArgumentException("Invalid API: $api. Use 'nager' or 'calendarific'");
        }
        self::$preferredAPI = $api;
    }

    /**
     * Get holidays for a country and year
     * 
     * @param string $countryCode ISO 3166-1 alpha-2 country code
     * @param int $year Year to get holidays for
     * @return array Array of holiday dates in Y-m-d format
     */
    public static function getHolidays(string $countryCode, int $year): array
    {
        $cacheKey = "{$countryCode}_{$year}";
        
        // Check cache first
        if (isset(self::$cache[$cacheKey]) && self::$cache[$cacheKey]['expires'] > time()) {
            return self::$cache[$cacheKey]['data'];
        }

        $holidays = [];

        // Try API first
        try {
            $holidays = self::fetchFromAPI($countryCode, $year);
        } catch (\Exception $e) {
            // Fallback to local data
            $holidays = self::getLocalHolidays($countryCode, $year);
        }

        // Cache the result
        self::$cache[$cacheKey] = [
            'data' => $holidays,
            'expires' => time() + self::CACHE_DURATION
        ];

        return $holidays;
    }

    /**
     * Fetch holidays from external API
     * 
     * @param string $countryCode ISO country code
     * @param int $year Year
     * @return array Array of holiday dates
     * @throws \Exception When API request fails
     */
    private static function fetchFromAPI(string $countryCode, int $year): array
    {
        if (self::$preferredAPI === 'nager') {
            return self::fetchFromNager($countryCode, $year);
        } else {
            return self::fetchFromCalendarific($countryCode, $year);
        }
    }

    /**
     * Fetch from Nager.Date API (free, no API key required)
     * 
     * @param string $countryCode ISO country code
     * @param int $year Year
     * @return array Array of holiday dates
     * @throws \Exception When API request fails
     */
    private static function fetchFromNager(string $countryCode, int $year): array
    {
        $url = self::NAGER_API_BASE . "/{$year}/{$countryCode}";
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'user_agent' => 'DateRangeHelper/1.0'
            ]
        ]);

        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            throw new \Exception("Failed to fetch holidays from Nager API");
        }

        $data = json_decode($response, true);
        
        if (!is_array($data)) {
            throw new \Exception("Invalid response from Nager API");
        }

        $holidays = [];
        foreach ($data as $holiday) {
            if (isset($holiday['date'])) {
                $holidays[] = $holiday['date'];
            }
        }

        return $holidays;
    }

    /**
     * Fetch from Calendarific API (requires API key)
     * 
     * @param string $countryCode ISO country code
     * @param int $year Year
     * @return array Array of holiday dates
     * @throws \Exception When API request fails
     */
    private static function fetchFromCalendarific(string $countryCode, int $year): array
    {
        if (self::$apiKey === null) {
            throw new \Exception("Calendarific API key not set. Use HolidayAPI::setApiKey()");
        }

        $url = self::CALENDARIFIC_API_BASE . "?api_key=" . self::$apiKey . "&country={$countryCode}&year={$year}";
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'user_agent' => 'DateRangeHelper/1.0'
            ]
        ]);

        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            throw new \Exception("Failed to fetch holidays from Calendarific API");
        }

        $data = json_decode($response, true);
        
        if (!isset($data['response']['holidays'])) {
            throw new \Exception("Invalid response from Calendarific API");
        }

        $holidays = [];
        foreach ($data['response']['holidays'] as $holiday) {
            if (isset($holiday['date']['iso'])) {
                $holidays[] = $holiday['date']['iso'];
            }
        }

        return $holidays;
    }

    /**
     * Get local fallback holidays for common countries
     * 
     * @param string $countryCode ISO country code
     * @param int $year Year
     * @return array Array of holiday dates
     */
    private static function getLocalHolidays(string $countryCode, int $year): array
    {
        $localHolidays = [
            'US' => [
                '01-01', // New Year's Day
                '01-15', // Martin Luther King Jr. Day (3rd Monday)
                '02-19', // Presidents' Day (3rd Monday)
                '05-27', // Memorial Day (Last Monday)
                '07-04', // Independence Day
                '09-02', // Labor Day (1st Monday)
                '10-14', // Columbus Day (2nd Monday)
                '11-11', // Veterans Day
                '11-28', // Thanksgiving Day (4th Thursday)
                '12-25', // Christmas Day
            ],
            'FR' => [
                '01-01', // New Year's Day
                '05-01', // Labor Day
                '05-08', // Victory in Europe Day
                '07-14', // Bastille Day
                '08-15', // Assumption Day
                '11-01', // All Saints' Day
                '11-11', // Armistice Day
                '12-25', // Christmas Day
            ],
            'DE' => [
                '01-01', // New Year's Day
                '05-01', // Labor Day
                '10-03', // German Unity Day
                '12-25', // Christmas Day
                '12-26', // Boxing Day
            ],
            'GB' => [
                '01-01', // New Year's Day
                '12-25', // Christmas Day
                '12-26', // Boxing Day
            ],
            'TR' => [
                '01-01', // New Year's Day
                '04-23', // National Sovereignty and Children's Day
                '05-01', // Labor Day
                '05-19', // Commemoration of Atatürk, Youth and Sports Day
                '07-15', // Democracy and National Unity Day
                '08-30', // Victory Day
                '10-29', // Republic Day
            ]
        ];

        if (!isset($localHolidays[$countryCode])) {
            return [];
        }

        $holidays = [];
        foreach ($localHolidays[$countryCode] as $date) {
            $holidays[] = "{$year}-{$date}";
        }

        return $holidays;
    }

    /**
     * Clear the holiday cache
     * 
     * @return void
     */
    public static function clearCache(): void
    {
        self::$cache = [];
    }

    /**
     * Get supported country codes
     * 
     * @return array Array of supported ISO country codes
     */
    public static function getSupportedCountries(): array
    {
        return [
            'US', 'FR', 'DE', 'GB', 'TR', // Local fallback
            // Add more as needed
        ];
    }

    /**
     * Check if a country is supported
     * 
     * @param string $countryCode ISO country code
     * @return bool True if supported
     */
    public static function isCountrySupported(string $countryCode): bool
    {
        return in_array(strtoupper($countryCode), self::getSupportedCountries());
    }
} 