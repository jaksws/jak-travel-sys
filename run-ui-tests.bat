@echo off
REM إعداد بيئة Laravel Dusk بشكل صحيح قبل تشغيل اختبارات Dusk على Windows

REM 1. التأكد من أن البيئة هي testing
set APP_ENV=testing
set DB_CONNECTION=sqlite
set DB_DATABASE=%cd%\database\database.sqlite

REM 2. إنشاء قاعدة بيانات SQLite إذا لم تكن موجودة
if not exist database\database.sqlite (
    type nul > database\database.sqlite
)

REM 3. تنظيف ذاكرة التخزين المؤقت
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

REM 4. تنفيذ الهجرات من جديد
php artisan migrate:fresh --seed --env=testing

REM 5. تشغيل اختبارات Dusk
php artisan dusk