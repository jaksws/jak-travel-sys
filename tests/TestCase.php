<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    // Remove the RefreshDatabase trait from the base TestCase class
    // Each test class that needs database refreshing should use it individually
    
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // حماية إضافية: لا تسمح بتشغيل اختبارات PHPUnit إلا في بيئة الاختبار
        if (!app()->environment('testing')) {
            throw new \Exception('PHPUnit tests can only be run in the testing environment!');
        }
        
        // Set up the app for testing
        // $this->withoutExceptionHandling(); // تم التعليق حتى تعمل اختبارات التحقق بشكل صحيح
    }
    
    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
