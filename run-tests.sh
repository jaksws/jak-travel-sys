#!/bin/bash

# مسار بسيط لتشغيل جميع الاختبارات مع إظهار التقارير التفصيلية
# قم بتنفيذ هذا الملف عند الحاجة للتأكد من عمل جميع عناصر النظام بشكل صحيح

echo "========== بدء تشغيل اختبارات نظام وكالات السفر =========="

# تنظيف ذاكرة التخزين المؤقت قبل تشغيل الاختبارات
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# تشغيل الاختبارات مع تقرير تفصيلي
php artisan test --coverage --min=80

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
echo "للمزيد من التفاصيل قم بالاطلاع على تقرير التغطية في المسار:"
echo "tests/coverage/index.html"