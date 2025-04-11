#!/bin/bash

# مسار بسيط لتشغيل جميع الاختبارات مع إظهار التقارير التفصيلية
# قم بتنفيذ هذا الملف عند الحاجة للتأكد من عمل جميع عناصر النظام بشكل صحيح

echo "========== بدء تشغيل اختبارات نظام وكالات السفر =========="

# تنظيف ذاكرة التخزين المؤقت قبل تشغيل الاختبارات
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# التحقق من وجود Xdebug أو PCOV
if php -r "echo (int)(extension_loaded('xdebug') || extension_loaded('pcov'));" | grep -q 1; then
  # تشغيل الاختبارات مع تقرير تغطية الشفرة
  echo ""
  echo "وجدت أداة لتغطية الشفرة (Xdebug/PCOV). سيتم إنشاء تقرير التغطية."
  php artisan test --coverage --min=80
else
  # تشغيل الاختبارات بدون تقرير تغطية الشفرة
  echo ""
  echo "لم يتم العثور على أداة لتغطية الشفرة (Xdebug/PCOV). سيتم تشغيل الاختبارات بدون تقرير تغطية."
  php artisan test
  
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
echo "تم تشغيل الاختبارات التالية:"
echo "- اختبارات النماذج (User, Agency, Service, Request, Quote)"
echo "- اختبارات المساعدين (CurrencyHelper, ServiceTypeHelper)"
echo "- اختبارات الخدمات (PaymentService, NotificationService)"
echo "- اختبارات الميزات (RequestManagement, Notifications)"
echo "- اختبارات واجهة برمجة التطبيقات API"
echo ""
if php -r "echo (int)(extension_loaded('xdebug') || extension_loaded('pcov'));" | grep -q 1; then
  echo "للمزيد من التفاصيل قم بالاطلاع على تقرير التغطية في المسار:"
  echo "tests/coverage/index.html"
fi