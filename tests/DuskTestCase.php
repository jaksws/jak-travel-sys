<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;

abstract class DuskTestCase extends BaseTestCase
{
    /**
     * Prepare for Dusk test execution.
     */
    #[BeforeClass]
    public static function prepare(): void
    {
        if (! static::runningInSail()) {
            static::startChromeDriver(['--port=9515']);
        }
    }

    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            $this->shouldStartMaximized() ? '--start-maximized' : '--window-size=1920,1080',
            '--disable-search-engine-choice-screen',
            '--disable-smooth-scrolling',
        ])->unless($this->hasHeadlessDisabled(), function (Collection $items) {
            return $items->merge([
                '--disable-gpu',
                '--headless=new',
            ]);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? env('DUSK_DRIVER_URL') ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }

    /**
     * Reset database before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // حماية إضافية: لا تسمح بتشغيل اختبارات Dusk إلا في بيئة الاختبار
        if (!app()->environment('testing')) {
            throw new \Exception('Dusk tests can only be run in the testing environment!');
        }

        // Ensure SQLite database file exists (for Dusk testing)
        if (env('DB_CONNECTION') === 'sqlite' && env('DB_DATABASE') && !str_contains(env('DB_DATABASE'), ':memory:')) {
            $dbPath = base_path(env('DB_DATABASE'));
            if (!file_exists($dbPath)) {
                // Create the SQLite file if it doesn't exist
                file_put_contents($dbPath, '');
            }
        }

        // Run fresh migrations
        Artisan::call('migrate:fresh');

        // Seed the database if needed
        Artisan::call('db:seed');
    }
}
