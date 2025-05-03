@echo off
REM Laravel Dusk UI Tests Runner - Windows

REM Ensure test database exists
IF NOT EXIST "database\testing.sqlite" (
    echo Creating database\testing.sqlite ...
    type nul > database\testing.sqlite
)

REM Install ChromeDriver version 135
php artisan dusk:chrome-driver 135
IF %ERRORLEVEL% NEQ 0 (
    echo Failed to install ChromeDriver. Exiting.
    exit /b 1
)

REM Fresh migrate and seed for testing environment
php artisan migrate:fresh --env=testing
IF %ERRORLEVEL% NEQ 0 (
    echo Migration failed! Exiting.
    exit /b 1
)
php artisan db:seed --env=testing
IF %ERRORLEVEL% NEQ 0 (
    echo Database seeding failed! Exiting.
    exit /b 1
)

REM Run Dusk tests on testing environment only
echo Running Dusk tests on testing environment...
php artisan dusk --env=testing
IF %ERRORLEVEL% NEQ 0 (
    echo Dusk tests failed on testing!
    exit /b 1
)

echo All tests passed successfully!
exit /b 0