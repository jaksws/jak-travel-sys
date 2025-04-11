# دليل إعداد المسؤول (Admin)

هذا الدليل يشرح كيفية إنشاء حساب مسؤول (Admin) بشكل صحيح في نظام وكالات السفر.

## إنشاء مستخدم مسؤول

لإنشاء مستخدم مسؤول جديد، قم بتنفيذ الأمر التالي:

```bash
php artisan app:setup-admin-user
```

### حل المشكلات الشائعة

#### المشكلة: يتم إنشاء المستخدم من نوع "عميل" بدلاً من "مسؤول"

وفقاً لهيكل قاعدة البيانات الخاصة بك، يوجد عمودان مهمان في جدول المستخدمين:
- `user_type`: يقبل القيم ('admin', 'agency', 'subagent', 'customer') مع قيمة افتراضية 'customer'
- `role`: يتم ضبطه افتراضياً على 'client'
- `is_admin`: قيمة منطقية (0/1) مضبوطة افتراضياً على '0'

لحل مشكلة تعيين المستخدم كعميل، يمكنك تعديل قيم هذه الأعمدة مباشرة في قاعدة البيانات:

```bash
# فتح قاعدة البيانات SQLite
sqlite3 database/database.sqlite

# عرض الجداول المتاحة
.tables

# عرض هيكل جدول المستخدمين
.schema users

# عرض أنواع المستخدمين الحالية
SELECT email, user_type, role, is_admin FROM users;

# تحديث نوع المستخدم ودوره وعلامة المسؤول لمستخدم محدد
UPDATE users SET 
  user_type = 'admin', 
  role = 'admin',
  is_admin = 1
WHERE email = 'admin@jaksws.com';

# التحقق من التغييرات
SELECT email, user_type, role, is_admin FROM users WHERE email = 'البريد_الإلكتروني_للمسؤول';

# الخروج من SQLite
.exit
```

#### ملاحظات مهمة حول أنواع المستخدمين

1. عمود `user_type` لديه قيود تحقق (CHECK constraint) تسمح فقط بالقيم التالية:
   - 'admin': مسؤول النظام
   - 'agency': وكيل رئيسي
   - 'subagent': سبوكيل
   - 'customer': عميل

2. يجب تعيين حقل `is_admin` إلى القيمة 1 للمستخدمين المسؤولين.

3. قد تحتاج أيضاً إلى تعيين `role` إلى 'admin' بدلاً من 'client' الافتراضي.

## الوصول إلى لوحة تحكم المسؤول

بعد تسجيل الدخول كمسؤول، يمكنك الوصول إلى لوحة التحكم عبر:

- الانتقال إلى `/admin` أو `/admin/dashboard`
- أو عبر النقر على "لوحة التحكم" في القائمة

## حل مشكلة "Target class [admin] does not exist"

إذا واجهتك رسالة الخطأ هذه، فهذا يشير إلى أن وسيط (middleware) `admin` غير معرف في النظام. تأكد من:

1. وجود ملف وسيط `AdminMiddleware.php` في المسار `app/Http/Middleware/`
2. تسجيل الوسيط في ملف `app/Http/Kernel.php` ضمن مصفوفة `$routeMiddleware`
3. تعريف المسارات الإدارية بشكل صحيح في ملف `routes/admin.php`

## التوجيه بعد تسجيل الدخول

للتأكد من توجيه المسؤولين إلى لوحة التحكم الخاصة بهم بعد تسجيل الدخول، تحقق من ملف:

`app/Providers/RouteServiceProvider.php`

وتأكد من تعريف المسار `HOME` بشكل صحيح، أو تعديل منطق التوجيه في:

`app/Http/Controllers/Auth/LoginController.php`
