#!/bin/bash
# Laravel Dusk UI Tests Runner - Linux/Mac

# تحديد مسار PHP
PHP_EXECUTABLE=php

# التحقق من البيئة الحالية
CURRENT_ENV=$($PHP_EXECUTABLE artisan env)
if [[ "$CURRENT_ENV" == *"production"* ]]; then
  echo "خطأ: لا يمكن تشغيل هذا السكربت في بيئة الإنتاج (Production)."
  exit 1
fi

# إعداد قاعدة بيانات الاختبار
if [ ! -d database ]; then
    echo "إنشاء مجلد قاعدة البيانات..."
    mkdir -p database
fi

if [ ! -f database/testing.sqlite ]; then
    echo "إنشاء قاعدة بيانات SQLite للاختبار..."
    touch database/testing.sqlite
fi

# إعداد متغيرات البيئة للاختبار
export DB_DATABASE=$(pwd)/database/testing.sqlite
export SESSION_DRIVER=array
export APP_URL=http://127.0.0.1:8001

# تثبيت ChromeDriver المناسب (135)
$PHP_EXECUTABLE artisan dusk:chrome-driver 135
if [ $? -ne 0 ]; then
    echo "فشل تثبيت ChromeDriver."
    exit 1
fi

# تنفيذ الهجرات من جديد مع seeder على قاعدة بيانات الاختبار
$PHP_EXECUTABLE artisan migrate:fresh --env=testing
if [ $? -ne 0 ]; then
    echo "فشل تنفيذ الهجرات!"
    exit 1
fi
$PHP_EXECUTABLE artisan db:seed --env=testing
if [ $? -ne 0 ]; then
    echo "فشل تنفيذ Seeder!"
    exit 1
fi

# تشغيل سيرفر Laravel على بورت 8001 في الخلفية
$PHP_EXECUTABLE artisan serve --env=testing --port=8001 > /dev/null 2>&1 &
SERVER_PID=$!
# الانتظار حتى يعمل السيرفر
sleep 5

echo "تشغيل اختبارات Dusk على بيئة testing..."
if [ -f ./vendor/bin/dusk ]; then
    $PHP_EXECUTABLE ./vendor/bin/dusk --env=testing
    TEST_RESULT=$?
else
    $PHP_EXECUTABLE artisan dusk --env=testing
    TEST_RESULT=$?
fi

# إيقاف السيرفر بعد انتهاء الاختبارات
kill $SERVER_PID

if [ $TEST_RESULT -ne 0 ]; then
    echo "فشلت اختبارات Dusk على بيئة testing!"
    exit 1
fi

echo "تم اجتياز جميع الاختبارات بنجاح!"
exit 0
