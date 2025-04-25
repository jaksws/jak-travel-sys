<?php

/**
 * فحص متطلبات نظام JAK Travel System
 */

echo "=== فحص متطلبات نظام JAK Travel System ===\n\n";

// عرض إصدار PHP
$php_version = PHP_VERSION;
$php_version_major = PHP_MAJOR_VERSION;
$php_version_minor = PHP_MINOR_VERSION;

echo "إصدار PHP: " . $php_version . "\n";

// فحص توافقية PHP
$php_compatible = version_compare($php_version, "8.2.0", ">=");
$php_recommended = version_compare($php_version, "8.3.0", ">=");
echo "- توافقية PHP: " . ($php_compatible ? "✅" : "❌") . " ";

if ($php_compatible) {
    if ($php_recommended) {
        echo "PHP {$php_version} (موصى به)\n";
    } else {
        echo "PHP {$php_version} (مدعوم، لكن يُنصح بالترقية إلى PHP 8.3+)\n";
    }
} else {
    echo "إصدار PHP غير مدعوم. يجب استخدام PHP 8.2.0 أو أحدث.\n";
}

// فحص حدود الذاكرة
$memory_limit = ini_get('memory_limit');
$memory_limit_bytes = return_bytes($memory_limit);
$memory_ok = $memory_limit_bytes >= 128 * 1024 * 1024; // 128MB

echo "- حد الذاكرة: " . ($memory_ok ? "✅" : "❌") . " {$memory_limit}";
if (!$memory_ok) {
    echo " (يُنصح بـ 128M على الأقل)";
}
echo "\n";

// فحص وقت التنفيذ
$max_execution_time = ini_get('max_execution_time');
$execution_time_ok = $max_execution_time >= 60 || $max_execution_time == 0; // 0 تعني غير محدود

echo "- الحد الأقصى لوقت التنفيذ: " . ($execution_time_ok ? "✅" : "❌") . " {$max_execution_time}s";
if (!$execution_time_ok) {
    echo " (يُنصح بـ 60 ثانية على الأقل)";
}
echo "\n\n";

// عرض جميع الامتدادات النشطة
$loaded_extensions = get_loaded_extensions();
echo "الامتدادات المفعلة: " . count($loaded_extensions) . "\n";

// تعريف الامتدادات المطلوبة
$required_extensions = [
    'pdo_mysql' => false,
    'zip' => false,
    'gd' => false,
    'openssl' => false,
    'json' => false,
    'mbstring' => false,
    'tokenizer' => false,
    'xml' => false,
    'fileinfo' => false,
    'intl' => false,
    'soap' => false
];

// فحص امتداد pdo_mysql بطريقتين
$pdo_mysql_loaded = extension_loaded('pdo_mysql');
$pdo_mysql_in_drivers = in_array('mysql', PDO::getAvailableDrivers());
$pdo_mysql = $pdo_mysql_loaded || $pdo_mysql_in_drivers;
$required_extensions['pdo_mysql'] = $pdo_mysql;

// التحقق من بقية الامتدادات المطلوبة
foreach ($required_extensions as $ext => $status) {
    if ($ext !== 'pdo_mysql') { // تم التحقق من pdo_mysql بالفعل
        $required_extensions[$ext] = extension_loaded($ext);
    }
    
    $status = $required_extensions[$ext];
    echo "- {$ext}: " . ($status ? "✅ متوفر" : "❌ غير متوفر") . "\n";
    
    if (!$status) {
        echo "  - لتثبيت {$ext}، استخدم الأمر المناسب لنظام التشغيل الخاص بك.\n";
    }
    
    if ($ext === 'pdo_mysql' && !$status) {
        echo "  - موجود في extension_loaded(): " . ($pdo_mysql_loaded ? "نعم" : "لا") . "\n";
        echo "  - موجود في PDO::getAvailableDrivers(): " . ($pdo_mysql_in_drivers ? "نعم" : "لا") . "\n";
        echo "  - سائقي PDO المتاحة: " . implode(", ", PDO::getAvailableDrivers()) . "\n";
    }
}

// تحديد نوع قاعدة البيانات المطلوب
$db_connection = getenv('DB_CONNECTION') ?: 'mysql';
$using_sqlite = $db_connection === 'sqlite';
echo "\n";

// فحص أذونات المجلدات الهامة
checkDirectoryPermissions();

// فحص اتصال قاعدة البيانات
try {
    if ($using_sqlite) {
        echo "- SQLite: التحقق من الاتصال... ";
        $db_path = getenv('DB_DATABASE') ?: __DIR__ . '/database/database.sqlite';
        if (!file_exists($db_path)) {
            echo "❌ ملف قاعدة البيانات غير موجود: {$db_path}\n";
        } else {
            $connection = new PDO("sqlite:{$db_path}");
            echo "✅ متوفر ومتصل بنجاح\n";
        }
    } else {
        echo "- MySQL: التحقق من الاتصال... ";
        $db_host = getenv('DB_HOST') ?: '127.0.0.1';
        $db_port = getenv('DB_PORT') ?: '3306';
        $db_username = getenv('DB_USERNAME') ?: 'root';
        $db_password = getenv('DB_PASSWORD') ?: '';
        
        if (!$pdo_mysql) {
            throw new PDOException("سائق PDO للـ MySQL غير متوفر. امتداد 'pdo_mysql' غير محمل.");
        }
        
        $connection = new PDO(
            "mysql:host={$db_host};port={$db_port}", 
            $db_username, 
            $db_password
        );
        echo "✅ متوفر ومتصل بنجاح\n";

        // التحقق من وجود قاعدة البيانات
        $db_name = getenv('DB_DATABASE') ?: 'jak_travel_sys';
        echo "- قاعدة البيانات '{$db_name}': التحقق... ";
        try {
            $checkDb = $connection->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$db_name}'");
            if ($checkDb->fetch()) {
                echo "✅ موجودة\n";
            } else {
                echo "❌ غير موجودة. يجب إنشاء قاعدة البيانات.\n";
            }
        } catch (PDOException $e) {
            echo "❌ تعذر التحقق: " . $e->getMessage() . "\n";
        }
    }
} catch (PDOException $e) {
    echo "❌ غير متوفر أو غير متصل: " . $e->getMessage() . "\n";
}

// تحقق من الإصدار وإظهار التوصيات
echo "\n=== ملخص ===\n";

$requirements_met = $php_compatible;
$missing_requirements = [];

if (!$php_compatible) {
    $missing_requirements[] = "PHP >= 8.2.0";
}

foreach ($required_extensions as $ext => $status) {
    if (!$status) {
        $requirements_met = false;
        $missing_requirements[] = $ext;
    }
}

if (isset($connection)) {
    echo "✅ الاتصال بقاعدة البيانات يعمل بشكل صحيح\n";
} else {
    echo "❌ فشل الاتصال بقاعدة البيانات\n";
    $requirements_met = false;
}

if ($requirements_met) {
    echo "\n✅ جميع المتطلبات الأساسية متوفرة. يمكنك المتابعة بتثبيت التطبيق.\n";
    
    // نصائح تحسين النظام
    if (!$php_recommended) {
        echo "\n⚠️ توصيات إضافية:\n";
        echo "- ترقية PHP إلى الإصدار 8.3 للحصول على أفضل أداء وميزات إضافية.\n";
    }
} else {
    echo "\n❌ بعض المتطلبات غير متوفرة: " . implode(", ", $missing_requirements) . "\n";
    
    // إظهار تعليمات التثبيت للمتطلبات المفقودة
    echo "\n=== تعليمات التثبيت ===\n";
    
    $php_version = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
    
    if (!$php_compatible) {
        echo "لترقية PHP إلى الإصدار 8.2 أو 8.3 (على Ubuntu/Debian):\n";
        echo "sudo add-apt-repository ppa:ondrej/php\n";
        echo "sudo apt-get update\n";
        echo "sudo apt-get install php8.3 php8.3-cli php8.3-common\n\n";
    }
    
    $missing_exts = array_keys(array_filter($required_extensions, function($v) { return !$v; }));
    if (!empty($missing_exts)) {
        echo "لتثبيت امتدادات PHP المفقودة (على Ubuntu/Debian):\n";
        $missing_extensions_cmd = [];
        
        foreach ($missing_exts as $ext) {
            if ($ext === 'pdo_mysql' && !$using_sqlite) {
                $missing_extensions_cmd[] = "php{$php_version}-mysql";
            } else {
                $missing_extensions_cmd[] = "php{$php_version}-{$ext}";
            }
        }
        
        echo "sudo apt-get update\n";
        echo "sudo apt-get install " . implode(" ", $missing_extensions_cmd) . "\n";
        $ext_names = array_map(function($ext) { 
            return $ext === 'pdo_mysql' ? 'mysql' : $ext; 
        }, $missing_exts);
        echo "sudo phpenmod -v {$php_version} " . implode(" ", $ext_names) . "\n";
        echo "sudo service php{$php_version}-fpm restart # أو apache2 restart إذا كنت تستخدم Apache\n\n";
    }
    
    if (!isset($connection) && !$using_sqlite) {
        echo "لتثبيت MySQL (على Ubuntu/Debian):\n";
        echo "sudo apt-get update\n";
        echo "sudo apt-get install mysql-server\n";
        echo "sudo mysql_secure_installation\n";
        echo "sudo systemctl start mysql\n";
        echo "sudo systemctl enable mysql\n\n";
        
        echo "إنشاء قاعدة بيانات جديدة:\n";
        echo "sudo mysql -e \"CREATE DATABASE jak_travel_sys CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\"\n";
        echo "sudo mysql -e \"GRANT ALL PRIVILEGES ON jak_travel_sys.* TO 'root'@'localhost';\"\n";
        echo "sudo mysql -e \"FLUSH PRIVILEGES;\"\n";
    } elseif (!isset($connection) && $using_sqlite) {
        echo "لإعداد SQLite:\n";
        echo "mkdir -p " . dirname(__DIR__ . '/database/database.sqlite') . "\n";
        echo "touch " . __DIR__ . "/database/database.sqlite\n";
        echo "chmod 666 " . __DIR__ . "/database/database.sqlite\n";
    }
}

// فحص php.ini للمساعدة في حل المشكلات
echo "\n=== معلومات تكوين PHP ===\n";
echo "مسار php.ini: " . php_ini_loaded_file() . "\n";
echo "مسارات البحث عن الامتدادات: " . ini_get('extension_dir') . "\n";

echo "\n=== الخطوات التالية بعد تثبيت المتطلبات ===\n";
echo "1. قم بنسخ ملف .env.example إلى .env: cp .env.example .env\n";
echo "2. قم بتعديل ملف .env وإضافة معلومات الاتصال بقاعدة البيانات\n";
echo "3. قم بتشغيل: composer install\n";
echo "4. قم بتشغيل: php artisan key:generate\n";
echo "5. قم بتشغيل: php artisan migrate\n";
echo "6. قم بتشغيل: php artisan db:seed (اختياري: لإضافة بيانات تجريبية)\n";

/**
 * وظيفة لتحويل أحجام الذاكرة (مثل 128M) إلى بايت
 */
function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $val = (int) $val;
    
    switch($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    
    return $val;
}

/**
 * وظيفة للتحقق من أذونات المجلدات الهامة
 */
function checkDirectoryPermissions() {
    $directories = [
        'storage' => __DIR__ . '/storage',
        'bootstrap/cache' => __DIR__ . '/bootstrap/cache',
        'public' => __DIR__ . '/public'
    ];
    
    echo "فحص أذونات المجلدات:\n";
    
    $all_writable = true;
    
    foreach ($directories as $name => $path) {
        if (file_exists($path)) {
            $writable = is_writable($path);
            echo "- {$name}: " . ($writable ? "✅ قابل للكتابة" : "❌ غير قابل للكتابة") . "\n";
            
            if (!$writable) {
                $all_writable = false;
            }
        } else {
            echo "- {$name}: ❌ المجلد غير موجود\n";
            $all_writable = false;
        }
    }
    
    if (!$all_writable) {
        echo "\nتعديل أذونات المجلدات:\n";
        echo "chmod -R 775 storage bootstrap/cache public\n";
        echo "chown -R www-data:www-data storage bootstrap/cache public # استبدل www-data بمستخدم خادم الويب المناسب\n\n";
    }
}