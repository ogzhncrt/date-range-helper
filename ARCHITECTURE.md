# 📁 Library Architecture

## 🏗️ Directory Structure

```
src/
├── DateRange.php          # Main DateRange class
├── DateRangeUtils.php     # Utility functions for range operations
└── Config/               # Configuration classes
    ├── TimezoneConfig.php    # Timezone configuration
    └── BusinessDayConfig.php # Business day configuration
```

## 🎯 Design Principles

### 1. **Separation of Concerns**
- **Core Logic**: `DateRange.php` and `DateRangeUtils.php` contain the main business logic
- **Configuration**: All configuration classes are organized in the `Config/` subdirectory
- **Tests**: Each class has corresponding test files in the `tests/` directory

### 2. **Configuration Management**
Configuration classes follow these principles:
- **Static Methods**: All configuration methods are static for easy access
- **Environment Variables**: Support for environment-based configuration
- **Validation**: Input validation with clear error messages
- **Reset Capability**: Ability to reset to default values for testing

### 3. **Namespace Organization**
```
Ogzhncrt\DateRangeHelper\          # Main namespace
├── DateRange                       # Core date range class
├── DateRangeUtils                  # Utility functions
└── Config\                        # Configuration namespace
    ├── TimezoneConfig             # Timezone configuration
    └── BusinessDayConfig          # Business day configuration
```

## 🔧 Configuration Classes

### TimezoneConfig
- **Purpose**: Manages timezone settings for date range operations
- **Environment Variable**: `DATE_RANGE_HELPER_TIMEZONE`
- **Default**: UTC
- **Key Methods**: `getTimezone()`, `setTimezone()`, `createDateTime()`

### BusinessDayConfig
- **Purpose**: Manages business day calculations and holiday settings
- **Environment Variables**: `DATE_RANGE_HELPER_WEEKEND_DAYS`, `DATE_RANGE_HELPER_HOLIDAYS`
- **Default**: Saturday and Sunday as weekends
- **Key Methods**: `isBusinessDay()`, `addHoliday()`, `loadHolidayCalendar()`

## 🚀 Benefits of This Structure

### 1. **Scalability**
- Easy to add new configuration classes in the `Config/` directory
- Clear separation between core logic and configuration
- Consistent naming and organization patterns

### 2. **Maintainability**
- Configuration classes are isolated and focused
- Easy to test individual configuration components
- Clear dependency relationships

### 3. **Developer Experience**
- Intuitive directory structure
- Consistent namespace patterns
- Easy to find and understand configuration options

## 🔮 Future Configuration Classes

As the library grows, additional configuration classes can be added:

```
src/Config/
├── TimezoneConfig.php
├── BusinessDayConfig.php
├── LocaleConfig.php          # Locale and formatting settings
├── ValidationConfig.php      # Validation rules and settings
└── CacheConfig.php          # Caching configuration
```

## 📋 Best Practices

### 1. **Configuration Class Guidelines**
- Use static methods for configuration access
- Provide environment variable support
- Include validation for all inputs
- Add reset functionality for testing
- Document all public methods with PHPDoc

### 2. **Testing Guidelines**
- Each configuration class should have its own test file
- Test both valid and invalid inputs
- Test environment variable functionality
- Test reset functionality
- Use setUp() and tearDown() to reset state

### 3. **Documentation Guidelines**
- Update README.md with new configuration options
- Include usage examples
- Document environment variables
- Provide migration guides for breaking changes

## 🔄 Migration Notes

When adding new configuration classes:
1. Place them in `src/Config/`
2. Use the namespace `Ogzhncrt\DateRangeHelper\Config\`
3. Follow the existing patterns for static methods and validation
4. Add comprehensive tests
5. Update this documentation 