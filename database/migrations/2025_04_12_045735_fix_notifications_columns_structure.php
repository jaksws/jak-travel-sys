<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // إذا كان الجدول موجودًا، نتحقق من وجود الأعمدة المطلوبة
        if (Schema::hasTable('notifications')) {
            $hasTitle = Schema::hasColumn('notifications', 'title');
            $hasMessage = Schema::hasColumn('notifications', 'message');
            $hasType = Schema::hasColumn('notifications', 'type');
            $hasLink = Schema::hasColumn('notifications', 'link');
            
            // عمل هجرة آمنة في حالة عدم وجود الأعمدة المطلوبة
            if (!$hasTitle || !$hasMessage || !$hasType || !$hasLink) {
                // لإدارة مشاكل SQLite، نحتاج إلى إنشاء جدول مؤقت بالهيكل الجديد ونقل البيانات
                Schema::create('temp_notifications', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('user_id')->constrained()->onDelete('cascade');
                    $table->string('title')->nullable();
                    $table->text('message')->nullable();
                    $table->string('type')->nullable();
                    $table->boolean('is_read')->default(false);
                    $table->json('data')->nullable();
                    $table->string('link')->nullable();
                    $table->timestamps();
                });
                
                // نسخ البيانات من الجدول الأصلي إلى الجدول المؤقت
                // هنا نضيف القيم الافتراضية للأعمدة المفقودة
                $oldNotifications = DB::table('notifications')->get();
                foreach ($oldNotifications as $notification) {
                    $newData = [
                        'id' => $notification->id,
                        'user_id' => $notification->user_id,
                        'is_read' => $notification->is_read ?? false,
                        'data' => $notification->data ?? '{}',
                        'created_at' => $notification->created_at,
                        'updated_at' => $notification->updated_at,
                    ];
                    
                    // إضافة الأعمدة الجديدة مع قيم افتراضية
                    $newData['title'] = $notification->title ?? 'إشعار جديد';
                    $newData['message'] = $notification->message ?? 'لديك إشعار جديد';
                    $newData['type'] = $notification->type ?? 'general';
                    $newData['link'] = $notification->link ?? null;
                    
                    DB::table('temp_notifications')->insert($newData);
                }
                
                // حذف الجدول الأصلي
                Schema::dropIfExists('notifications');
                
                // إعادة تسمية الجدول المؤقت ليصبح هو الجدول الأساسي
                Schema::rename('temp_notifications', 'notifications');
            }
        } else {
            // في حالة عدم وجود الجدول، نقوم بإنشائه من الصفر
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('title');
                $table->text('message')->nullable();
                $table->string('type')->nullable();
                $table->boolean('is_read')->default(false);
                $table->json('data')->nullable();
                $table->string('link')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // هنا لا داعي لتنفيذ عملية عكسية، لأن هذه الهجرة تصلح مشكلة في الهيكل
    }
};
