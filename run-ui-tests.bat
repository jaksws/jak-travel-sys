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

REM Start Laravel server for testing on port 8001
start /min php artisan serve --env=testing --port=8001
REM Wait a few seconds to ensure the server is up
ping 127.0.0.1 -n 6 > nul

REM Run Dusk tests on testing environment only
REM Make sure APP_URL is set to http://127.0.0.1:8001 in .env.testing
set APP_URL=http://127.0.0.1:8001

echo Running Dusk tests on testing environment...
php artisan dusk --env=testing
set TEST_RESULT=%ERRORLEVEL%

REM Kill the Laravel server process (find by window title)
taskkill /FI "WINDOWTITLE eq php artisan serve*" /F >nul 2>&1

IF %TEST_RESULT% NEQ 0 (
    echo Dusk tests failed on testing!
    exit /b 1
)

echo All tests passed successfully!
exit /b 0