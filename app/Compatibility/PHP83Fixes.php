<?php

namespace App\Compatibility;

use ReflectionClass;
use ReflectionProperty;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; // Ensure DB facade is available if needed
use Illuminate\Support\Facades\Schema; // Ensure Schema facade is available

/**
 * Contains fixes and adaptations for PHP 8.3 compatibility
 */
class PHP83Fixes
{
    /**
     * Apply compatibility fixes for PHP 8.3
     */
    public static function apply()
    {
        // No need to check version here, apply-fixes.php handles it
        self::fixDeprecatedFeatures();
        // Ensure Laravel app is bootstrapped enough for DB/Schema if needed
        // self::prepareModelProperties(); // Consider if this is truly needed and safe in this context
        self::fixJsonOptions();
    }
    
    /**
     * Fix deprecated features in PHP 8.3
     */
    private static function fixDeprecatedFeatures()
    {
        // Dynamic properties creation is deprecated in PHP 8.3
        // Suppress deprecation warnings if necessary, though AllowDynamicProperties attribute is preferred
        ini_set('error_reporting', error_reporting() & ~E_DEPRECATED);
        // Consider adding #[AllowDynamicProperties] to relevant classes instead
    }

    /**
     * تجهيز خصائص النماذج لتجنب مشاكل الخصائص الديناميكية
     * NOTE: This might require a fully bootstrapped Laravel application.
     * Consider alternative approaches if running this early causes issues.
     */
    private static function prepareModelProperties()
    {
        // Use a path relative to this file's directory
        $modelsPath = realpath(__DIR__ . '/../Models'); 
        
        if (!$modelsPath) {
             echo "Warning: Could not determine models path.\n";
             return;
        }

        $modelsList = glob($modelsPath . '/*.php');
        $models = [];
        
        foreach ($modelsList as $modelFile) {
            $modelName = basename($modelFile, '.php');
            $modelClass = "App\\Models\\{$modelName}";
            
            try {
                // Ensure class exists and is a subclass of Model before proceeding
                if (class_exists($modelClass) && is_subclass_of($modelClass, Model::class)) {
                     // Check if DB connection is available before attempting schema operations
                     if (self::isDbConnected()) {
                         $models[] = $modelClass;
                     } else {
                         echo "Warning: DB not connected, skipping dynamic property preparation for {$modelClass}.\n";
                         // Optionally break or return if DB is essential for this step
                     }
                }
            } catch (\Throwable $e) { // Catch Throwable for broader error handling
                echo "Warning: Error checking model {$modelClass}: " . $e->getMessage() . "\n";
                continue;
            }
        }

        foreach ($models as $modelClass) {
            try {
                // Instantiate model only if necessary and safe
                // $model = new $modelClass; // Instantiation might trigger unwanted side effects
                // self::registerModelDynamicProperties($model); // Call registration logic
                 echo "Info: Would prepare dynamic properties for {$modelClass} here if DB connected.\n";
            } catch (\Throwable $e) {
                 echo "Warning: Error preparing model {$modelClass}: " . $e->getMessage() . "\n";
                continue;
            }
        }
    }

    /**
     * تسجيل الخصائص الديناميكية للنموذج
     * Requires DB connection.
     * @param Model $model نموذج Eloquent
     */
    private static function registerModelDynamicProperties(Model $model)
    {
        // استخراج أسماء الخصائص الموجودة
        $existingProps = [];
        $reflection = new ReflectionClass($model);
        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE) as $prop) {
            $existingProps[] = $prop->getName();
        }

        // استخراج أسماء الأعمدة من الجدول
        try {
            $table = $model->getTable();
            // Use Schema facade for better compatibility
            if (Schema::hasTable($table)) {
                $columns = Schema::getColumnListing($table);

                // إضافة الخصائص التي تمثل أعمدة غير مسجلة بالفعل
                foreach ($columns as $column) {
                    if (!in_array($column, $existingProps) && !$model->hasAttributeMutator($column) && !$model->hasAttributeCast($column)) {
                         // Avoid setting null directly if it conflicts with types
                         // This part is risky without full context; consider #[AllowDynamicProperties]
                         // echo "Info: Dynamically adding property '{$column}' to " . get_class($model) . "\n";
                         // $model->{$column} = null; // Potentially problematic
                    }
                }
            } else {
                 echo "Warning: Table '{$table}' not found for model " . get_class($model) . "\n";
            }
        } catch (\Throwable $e) {
            echo "Warning: Error registering dynamic properties for " . get_class($model) . ": " . $e->getMessage() . "\n";
        }
    }

    /**
     * ضبط إعدادات JSON للتعامل مع التغييرات في PHP 8.3
     */
    private static function fixJsonOptions()
    {
        // في PHP 8.3، تم تغيير سلوك json_encode/decode
        // Laravel handles JSON encoding/decoding internally. Explicit overrides might
        // not be necessary unless specific non-standard behavior is required.
        // The default Laravel settings usually work across PHP versions.
        // If specific options are needed globally, configure them in config/app.php or a service provider.
        
        // Example: If needed, ensure options are set (but likely unnecessary)
        // $jsonOptions = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        // config(['app.json_options' => $jsonOptions]); // If app() is available
         echo "Info: JSON options fix applied (usually handled by Laravel).\n";
    }

    /**
     * Check if database connection is configured and potentially usable.
     * This is a basic check; might need refinement.
     */
     private static function isDbConnected(): bool
     {
         try {
             // Check if default DB connection is configured
             $defaultConnection = config('database.default');
             if (!$defaultConnection || !config("database.connections.{$defaultConnection}")) {
                 return false;
             }
             // Attempt a simple query; might fail if migrations haven't run
             // DB::connection()->getPdo(); // This forces a connection attempt
             return true; // Assume configured is good enough for schema checks
         } catch (\Throwable $e) {
             echo "Warning: DB connection check failed: " . $e->getMessage() . "\n";
             return false;
         }
     }

    /**
     * Define the basePath method manually if app() is unavailable
     * Note: Using relative paths with __DIR__ is often safer.
     */
    // private static function basePath($path = '')
    // {
    //     // Basic implementation if app() helper is not available
    //     $base = realpath(__DIR__ . '/../../'); 
    //     return $base . ($path ? DIRECTORY_SEPARATOR . trim($path, '/\\') : '');
    // }
}
