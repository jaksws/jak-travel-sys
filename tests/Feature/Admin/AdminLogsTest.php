<?php

namespace Tests\Feature\Admin;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class AdminLogsTest extends AdminTestCase
{
    protected $tempLogFile;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a temporary log file for testing
        $this->tempLogFile = storage_path('logs/test_log_' . time() . '.log');
        File::put($this->tempLogFile, "Test log entry 1\nTest log entry 2\nTest log entry 3\n");
    }

    protected function tearDown(): void
    {
        // Clean up the temporary log file
        if (File::exists($this->tempLogFile)) {
            File::delete($this->tempLogFile);
        }
        
        parent::tearDown();
    }

    /**
     * Test that admin can view logs page.
     *
     * @return void
     */
    public function test_admin_can_view_logs_page()
    {
        $this->loginAsAdmin();
        
        $response = $this->get(route('admin.system.logs'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.logs');
        $response->assertViewHas('logFiles');
    }
    
    /**
     * Test that admin can view specific log file.
     *
     * @return void
     */
    public function test_admin_can_view_specific_log_file()
    {
        $this->loginAsAdmin();
        
        $logFileName = basename($this->tempLogFile);
        
        $response = $this->get(route('admin.system.logs', ['log' => $logFileName]));
        $response->assertStatus(200);
        $response->assertViewIs('admin.logs');
        $response->assertViewHas('selectedLog', $logFileName);
        $response->assertViewHas('logContent');
        
        // Verify the log content is included in the response
        $logContent = $response->viewData('logContent');
        $this->assertStringContainsString('Test log entry 1', $logContent);
        $this->assertStringContainsString('Test log entry 2', $logContent);
    }
    
    /**
     * Test that admin cannot view logs outside of the logs directory.
     *
     * @return void
     */
    public function test_admin_cannot_view_logs_outside_directory()
    {
        $this->loginAsAdmin();
        
        // Try to access a file outside the logs directory
        $response = $this->get(route('admin.system.logs', ['log' => '../.env']));
        
        // Should not expose the .env file
        $response->assertStatus(200);
        $response->assertViewHas('logContent', null);
    }
    
    /**
     * Test that all log files are listed.
     *
     * @return void
     */
    public function test_all_log_files_are_listed()
    {
        $this->loginAsAdmin();
        
        $response = $this->get(route('admin.system.logs'));
        $response->assertStatus(200);
        
        $logFiles = $response->viewData('logFiles');
        
        // Verify our test log file is in the list
        $testLogFound = false;
        foreach ($logFiles as $logFile) {
            if (basename($logFile) === basename($this->tempLogFile)) {
                $testLogFound = true;
                break;
            }
        }
        
        $this->assertTrue($testLogFound, 'Test log file should be in the list of log files');
    }
}