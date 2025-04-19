#!/bin/bash

# اكتشاف نظام التشغيل
OS=$(uname -s)
case "$OS" in
  Linux*)     OS_TYPE="Linux";;
  Darwin*)    OS_TYPE="macOS";;
  CYGWIN*|MINGW*|MSYS*) OS_TYPE="Windows";;
  *)          OS_TYPE="Unknown";;
esac

echo "========== بدء تشغيل اختبارات نظام وكالات السفر =========="
echo "نظام التشغيل: $OS_TYPE"

# التحقق من وجود PHP في البيئة
PHP_EXECUTABLE=$(command -v php || echo "/e/laragon-6.0.0/bin/php/php-8.2.6-Win32-vs16-x64/php.exe")

if [ ! -x "$PHP_EXECUTABLE" ]; then
  echo "خطأ: لم يتم العثور على PHP في البيئة الحالية. تأكد من تثبيت PHP وإضافته إلى PATH."
  exit 1
fi

echo "استخدام PHP من: $PHP_EXECUTABLE"

# تنظيف ذاكرة التخزين المؤقت قبل تشغيل الاختبارات
$PHP_EXECUTABLE artisan cache:clear
$PHP_EXECUTABLE artisan config:clear
$PHP_EXECUTABLE artisan route:clear
$PHP_EXECUTABLE artisan view:clear

# التحقق من وجود Composer
COMPOSER_EXECUTABLE=$(command -v composer || echo "composer")
if [ ! -x "$COMPOSER_EXECUTABLE" ]; then
  echo "خطأ: لم يتم العثور على Composer. تأكد من تثبيته."
  exit 1
fi

echo "استخدام Composer من: $COMPOSER_EXECUTABLE"

# تثبيت الاعتماديات
$COMPOSER_EXECUTABLE install

# التحقق من وجود Node.js
NODE_EXECUTABLE=$(command -v node || echo "node")
if [ ! -x "$NODE_EXECUTABLE" ]; then
  echo "خطأ: لم يتم العثور على Node.js. تأكد من تثبيته."
  exit 1
fi

echo "استخدام Node.js من: $NODE_EXECUTABLE"

# تثبيت الاعتماديات الأمامية
npm install

# تشغيل الاختبارات
if $PHP_EXECUTABLE -r "echo (int)(extension_loaded('xdebug') || extension_loaded('pcov'));" | grep -q 1; then
  echo ""
  echo "وجدت أداة لتغطية الشفرة (Xdebug/PCOV). سيتم إنشاء تقرير التغطية."
  $PHP_EXECUTABLE artisan test --coverage --min=80
else
  echo ""
  echo "لم يتم العثور على أداة لتغطية الشفرة (Xdebug/PCOV). سيتم تشغيل الاختبارات بدون تقرير تغطية."
  $PHP_EXECUTABLE artisan test

  echo ""
  echo "لإضافة دعم تغطية الشفرة، يمكنك تثبيت Xdebug باستخدام الأمر:"
  echo "sudo apt-get install php-xdebug"
  echo "أو"
  echo "sudo pecl install xdebug"
  echo "ثم إعادة تشغيل PHP-FPM أو سيرفر الويب."
fi

# عرض تقرير مختصر بعد انتهاء الاختبارات
echo ""
echo "========== ملخص الاختبارات =========="
echo "تم تشغيل الاختبارات بنجاح."