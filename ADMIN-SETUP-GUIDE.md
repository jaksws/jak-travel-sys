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
- `role`: يتم ضبطه افتراضياً على 'admin'
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
WHERE email = 'البريد_الإلكتروني_للمسؤول';

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

3. قد تحتاج أيضاً إلى التأكد من أن قيمة `role` مضبوطة على 'admin'.

## الوصول إلى لوحة تحكم المسؤول

بعد تسجيل الدخول كمسؤول، يمكنك الوصول إلى لوحة التحكم عبر:

- الانتقال إلى `/admin` أو `/admin/dashboard`
- أو عبر النقر على "لوحة التحكم" في القائمة

## حل مشكلة "Target class [admin] does not exist"

إذا واجهتك رسالة الخطأ هذه، فهذا يشير إلى مشكلة في تسجيل وسيط (middleware) `admin` في النظام. لحل المشكلة، اتبع الخطوات التالية:

1. تأكد من وجود ملف الوسيط في المسار الصحيح:
   ```
   app/Http/Middleware/AdminMiddleware.php
   ```

2. تحقق من أن الملف يحتوي على تعريف الصف الصحيح:
   ```php
   <?php

   namespace App\Http\Middleware;

   use Closure;
   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\Auth;

   class AdminMiddleware
   {
       public function handle(Request $request, Closure $next)
       {
           if (!Auth::check() || 
               Auth::user()->user_type !== 'admin' && 
               Auth::user()->role !== 'admin' && 
               Auth::user()->is_admin !== 1) {
               return redirect('/')->with('error', 'لا تملك صلاحية الوصول');
           }
           
           return $next($request);
       }
   }
   ```

3. تأكد من تسجيل الوسيط بشكل صحيح في `app/Http/Kernel.php`:
   ```php
   protected $routeMiddleware = [
       // ... الوسطاء الأخرى ...
       'admin' => \App\Http\Middleware\AdminMiddleware::class,
   ];
   ```

4. أعد تشغيل ذاكرة التخزين المؤقت للتطبيق:
   ```bash
   php artisan optimize:clear
   ```

5. تأكد من أن مسارات الإدارة تستخدم الوسيط بشكل صحيح في ملف `routes/admin.php`:
   ```php
   Route::middleware(['web', 'auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
       // ... المسارات ...
   });
   ```

6. تأكد من تحميل ملف `routes/admin.php` في مزود خدمة التوجيه:
   ```bash
   # افتح ملف مزود خدمة التوجيه
   nano app/Providers/RouteServiceProvider.php
   
   # تأكد من تحميل ملف المسارات الإدارية
   $this->routes(function () {
       // ... المسارات الأخرى ...
       Route::middleware('web')
           ->group(base_path('routes/admin.php'));
   });
   ```

## التوجيه بعد تسجيل الدخول

للتأكد من توجيه المسؤولين إلى لوحة التحكم الخاصة بهم بعد تسجيل الدخول، تحقق من ملف:

`app/Providers/RouteServiceProvider.php`

وتأكد من تعريف المسار `HOME` بشكل صحيح، أو تعديل منطق التوجيه في:

`app/Http/Controllers/Auth/LoginController.php`

## خطوات سريعة لإصلاح جميع المشكلات

لحل جميع المشكلات المتعلقة بالمسؤول دفعة واحدة، نفذ الأوامر التالية:

```bash
# مسح ذاكرة التخزين المؤقت
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# تحديث المستخدم الحالي ليكون مسؤولاً
# استبدل 'your-email@example.com' بالبريد الإلكتروني الخاص بك
sqlite3 database/database.sqlite "UPDATE users SET user_type='admin', role='admin', is_admin=1 WHERE email='your-email@example.com'"

# إعادة تشغيل خادم التطوير
php artisan serve
```
