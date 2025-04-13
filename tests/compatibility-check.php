<?php

/**
 * Check PHP 8.3 compatibility
 * 
 * This script can be used to identify common PHP 8.3 compatibility issues
 * and verify system requirements for نظام وكالات السفر (RTLA)
 */

echo "===================================================\n";
echo "نظام وكالات السفر (RTLA) - فحص التوافق والمتطلبات\n";
echo "===================================================\n\n";

// Check PHP version
$phpVersion = phpversion();
$minVersion = '8.2.0';
$recommendedVersion = '8.3.0';
$isVersionOk = version_compare($phpVersion, $minVersion, '>=');

echo "• PHP الإصدار: $phpVersion\n";
echo "  الإصدار المطلوب: $minVersion أو أعلى\n";
echo "  الإصدار الموصى به: $recommendedVersion أو أعلى\n";
echo "  النتيجة: " . ($isVersionOk ? "✅ متوافق" : "❌ غير متوافق") . "\n\n";

// Check required extensions
echo "• التحقق من التوسعات المطلوبة:\n";
$requiredExtensions = [
    'pdo', 'pdo_mysql', 'pdo_sqlite', 'mbstring', 'openssl', 'json', 
    'curl', 'fileinfo', 'zip', 'xml', 'tokenizer', 'ctype', 'gd'
];

$missingExtensions = [];
foreach ($requiredExtensions as $extension) {
    $loaded = extension_loaded($extension);
    echo "  - $extension: " . ($loaded ? "✅ متوفر" : "❌ غير متوفر") . "\n";
    if (!$loaded) {
        $missingExtensions[] = $extension;
    }
}

if (count($missingExtensions) > 0) {
    echo "\n  ⚠️ توجد توسعات مفقودة: " . implode(', ', $missingExtensions) . "\n";
    echo "  يجب تثبيت هذه التوسعات لضمان عمل النظام بشكل صحيح.\n";
}
echo "\n";

// Check memory limit
$memoryLimit = ini_get('memory_limit');
$memoryLimitBytes = return_bytes($memoryLimit);
$recommendedMemory = 128 * 1024 * 1024; // 128MB

echo "• الذاكرة المتاحة: $memoryLimit\n";
echo "  القيمة الموصى بها: 128M أو أكثر\n";
echo "  النتيجة: " . ($memoryLimitBytes >= $recommendedMemory ? "✅ كافية" : "⚠️ قد لا تكون كافية") . "\n\n";

// Check max execution time
$maxExecutionTime = ini_get('max_execution_time');
$recommendedExecutionTime = 60;

echo "• الحد الأقصى لوقت التنفيذ: " . ($maxExecutionTime == 0 ? "غير محدود" : "$maxExecutionTime ثانية") . "\n";
echo "  القيمة الموصى بها: $recommendedExecutionTime ثانية أو أكثر\n";
echo "  النتيجة: " . (($maxExecutionTime == 0 || $maxExecutionTime >= $recommendedExecutionTime) ? "✅ مناسب" : "⚠️ قد يكون غير كافي") . "\n\n";

// Check database support
echo "• دعم قواعد البيانات:\n";
$hasMysql = extension_loaded('pdo_mysql');
$hasSqlite = extension_loaded('pdo_sqlite');

echo "  - MySQL: " . ($hasMysql ? "✅ مدعوم" : "❌ غير مدعوم") . "\n";
echo "  - SQLite: " . ($hasSqlite ? "✅ مدعوم" : "❌ غير مدعوم") . "\n";

if (!$hasMysql && !$hasSqlite) {
    echo "  ❌ يجب توفر دعم لإحدى قواعد البيانات على الأقل: MySQL أو SQLite\n";
} else {
    echo "  ✅ تم العثور على دعم لقاعدة بيانات واحدة على الأقل\n";
}
echo "\n";

// Check directory permissions
echo "• التحقق من أذونات المجلدات:\n";

$directories = [
    'storage' => __DIR__ . '/../storage',
    'bootstrap/cache' => __DIR__ . '/../bootstrap/cache',
    'public' => __DIR__ . '/../public',
];

$writeableDirs = 0;
$totalDirs = count($directories);

foreach ($directories as $name => $dir) {
    $isWriteable = is_dir($dir) && is_writable($dir);
    echo "  - $name: " . ($isWriteable ? "✅ قابل للكتابة" : "❌ غير قابل للكتابة") . "\n";
    if ($isWriteable) {
        $writeableDirs++;
    }
}

if ($writeableDirs === $totalDirs) {
    echo "  ✅ جميع المجلدات لديها الأذونات المناسبة\n";
} else {
    echo "  ⚠️ بعض المجلدات تحتاج إلى تعديل الأذونات\n";
}
echo "\n";

// Check class name resolution in constants
class TestClass {
    // In PHP 8.3, this syntax was standardized
    const TEST_CLASS = self::class;
    
    public static function checkCompatibility() {
        try {
            // Check readonly properties handling (PHP 8.2+)
            if (version_compare($GLOBALS['phpVersion'], '8.2.0', '>=')) {
                echo "• اختبار التوافق مع خصائص القراءة فقط (readonly): ✅ مدعوم\n";
            } else {
                echo "• اختبار التوافق مع خصائص القراءة فقط (readonly): ⚠️ غير مدعوم بالكامل\n";
            }
            
            // Check enums support (PHP 8.1+)
            if (version_compare($GLOBALS['phpVersion'], '8.1.0', '>=')) {
                echo "• دعم Enums: ✅ مدعوم\n";
            } else {
                echo "• دعم Enums: ❌ غير مدعوم\n";
            }
            
            // Check for fibers support (PHP 8.1+)
            if (version_compare($GLOBALS['phpVersion'], '8.1.0', '>=') && class_exists('Fiber')) {
                echo "• دعم Fibers: ✅ مدعوم\n";
            } else {
                echo "• دعم Fibers: ❌ غير مدعوم\n";
            }
        } catch (Throwable $e) {
            echo "حدث خطأ أثناء اختبار التوافق: " . $e->getMessage() . "\n";
        }
    }
}

// Run test to check compatibility
TestClass::checkCompatibility();

echo "\n===================================================\n";
// Overall assessment
$hasCriticalIssues = !$isVersionOk || count($missingExtensions) > 1;

if ($hasCriticalIssues) {
    echo "❌ توجد مشاكل حرجة تحتاج إلى معالجة قبل تثبيت النظام\n";
} else if (count($missingExtensions) === 1 || $writeableDirs < $totalDirs) {
    echo "⚠️ يمكن تثبيت النظام ولكن قد تواجه بعض المشاكل\n";
} else {
    echo "✅ النظام الخاص بك يستوفي جميع المتطلبات\n";
}
echo "===================================================\n";

// Helper function to convert memory values to bytes
function return_bytes($size_str) {
    switch (substr($size_str, -1)) {
        case 'K':
        case 'k':
            return (int)$size_str * 1024;
        case 'M':
        case 'm':
            return (int)$size_str * 1048576;
        case 'G':
        case 'g':
            return (int)$size_str * 1073741824;
        default:
            return (int)$size_str;
    }
}
