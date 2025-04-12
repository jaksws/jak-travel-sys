<?php

namespace Tests\Feature\Admin;

use PHPUnit\Framework\TestSuite;
use PHPUnit\TextUI\TestRunner;

class AdminTestSuite
{
    /**
     * Create a test suite for admin functionality.
     *
     * @return TestSuite
     */
    public static function suite()
    {
        $suite = new TestSuite('Admin Dashboard Tests');
        
        // Add all admin tests
        $suite->addTestFile(__DIR__ . '/AdminAuthTest.php');
        $suite->addTestFile(__DIR__ . '/AdminDashboardTest.php');
        $suite->addTestFile(__DIR__ . '/AdminUserManagementTest.php');
        $suite->addTestFile(__DIR__ . '/AdminRequestManagementTest.php');
        $suite->addTestFile(__DIR__ . '/AdminSettingsTest.php');
        $suite->addTestFile(__DIR__ . '/AdminLogsTest.php');
        $suite->addTestFile(__DIR__ . '/AdminDashboardUITest.php');
        
        return $suite;
    }
}