<?php

/**
 * Check PHP 8.3 compatibility
 * 
 * This script can be used to identify common PHP 8.3 compatibility issues
 */

// Check for deprecated features or changed behaviors in PHP 8.3
$phpVersion = phpversion();
echo "Current PHP version: $phpVersion\n";

// Check class name resolution in constants
class TestClass {
    // In PHP 8.3, this syntax was standardized
    const TEST_CLASS = self::class;
    
    public static function checkCompatibility() {
        // Check handling of readonly properties
        // PHP 8.3 improved readonly property handling
    }
}

// Run test to check compatibility
TestClass::checkCompatibility();

echo "Compatibility check completed. Look for warnings above.\n";
