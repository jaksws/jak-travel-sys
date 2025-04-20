<?php

namespace App\Compatibility;

use ReflectionClass;
use ReflectionProperty;
use Illuminate\Database\Eloquent\Model;

/**
 * Contains fixes and adaptations for PHP 8.3 compatibility
 */
class PHP83Fixes
{
    /**
     * Apply compatibility fixes based on PHP version
     */
    public static function apply()
    {
        if (version_compare(PHP_VERSION, '8.3.0', '>=')) {
            // Apply PHP 8.3 specific fixes
            self::fixDeprecatedFeatures();
            self::prepareModelProperties();
            self::fixJsonOptions();
        }
    }
    
    /**
     * Fix deprecated features in PHP 8.3
     */
    private static function fixDeprecatedFeatures()
    {
        // تعطيل التحذيرات الخاصة بإنشاء الخصائص الديناميكية
        // Dynamic properties creation is deprecated in PHP 8.3
        ini_set('error_reporting', error_reporting() & ~E_DEPRECATED);
    }

    /**
     * تجهيز خصائص النماذج لتجنب مشاكل الخصائص الديناميكية
     */
    private static function prepareModelProperties()
    {
        // قائمة بالنماذج التي نحتاج لتجهيزها
        $modelsPath = app()->basePath('app/Models'); // Adjusted from app_path('Models/*.php')
        
        // Retrieve the list of model files
        $modelsList = glob($modelsPath . '/*.php');
        $models = [];
        
        foreach ($modelsList as $modelFile) {
            $modelName = basename($modelFile, '.php');
            $modelClass = "App\\Models\\{$modelName}";
            
            try {
                if (class_exists($modelClass) && is_subclass_of($modelClass, Model::class)) {
                    $models[] = $modelClass;
                }
            } catch (\Exception $e) {
                // Ignore exceptions to prevent script failures
                continue;
            }
        }

        foreach ($models as $modelClass) {
            try {
                $model = new $modelClass;
                self::registerModelDynamicProperties($model);
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    /**
     * تسجيل الخصائص الديناميكية للنموذج
     * 
     * @param Model $model نموذج Eloquent
     */
    private static function registerModelDynamicProperties(Model $model)
    {
        // استخراج أسماء الخصائص الموجودة
        $existingProps = [];
        $reflection = new ReflectionClass($model);
        foreach ($reflection->getProperties() as $prop) {
            $existingProps[] = $prop->getName();
        }

        // استخراج أسماء الأعمدة من الجدول
        try {
            $table = $model->getTable();
            $columns = \DB::getSchemaBuilder()->getColumnListing($table);

            // إضافة الخصائص التي تمثل أعمدة غير مسجلة بالفعل
            foreach ($columns as $column) {
                if (!in_array($column, $existingProps)) {
                    $model->{$column} = null; // إضافة الخاصية
                }
            }
        } catch (\Exception $e) {
            // تجاهل الأخطاء لتجنب توقف السكريبت
        }
    }

    /**
     * ضبط إعدادات JSON للتعامل مع التغييرات في PHP 8.3
     */
    private static function fixJsonOptions()
    {
        // في PHP 8.3، تم تغيير سلوك json_encode/decode
        // تأكد من ضبط الخيارات المناسبة افتراضياً
        
        // Laravel قد يستخدم json_encode في أماكن كثيرة، لذا نضبط معالج عام لتنسيق JSON
        $jsonOptions = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        
        if (!app()->runningInConsole()) {
            // تطبيق الإعدادات فقط عند تشغيل التطبيق الفعلي وليس من سطر الأوامر
            config(['json_options' => $jsonOptions]);
            
            // تسجيل معالج للتعامل مع JSON بشكل موحد
            app()->singleton('json.handler', function ($app) use ($jsonOptions) {
                return new class($jsonOptions) {
                    protected $options;
                    
                    public function __construct($options)
                    {
                        $this->options = $options;
                    }
                    
                    public function encode($value)
                    {
                        return json_encode($value, $this->options);
                    }
                    
                    public function decode($json, $assoc = false)
                    {
                        return json_decode($json, $assoc);
                    }
                };
            });
        }
    }
}
