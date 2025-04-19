#!/bin/bash

# سكريبت تثبيت متطلبات نظام JAK Travel System
echo "=== تثبيت متطلبات نظام JAK Travel System ==="
echo "=================================================="

# تحديد نظام التشغيل
detect_os() {
    if [ -f /etc/os-release ]; then
        . /etc/os-release
        OS_NAME="$NAME"
        OS_VERSION="$VERSION_ID"
        
        # تبسيط اسم نظام التشغيل للتعامل معه
        if [[ "$OS_NAME" == *"Ubuntu"* ]]; then
            OS_TYPE="ubuntu"
        elif [[ "$OS_NAME" == *"Debian"* ]]; then
            OS_TYPE="debian"
        elif [[ "$OS_NAME" == *"CentOS"* ]]; then
            OS_TYPE="centos"
        elif [[ "$OS_NAME" == *"Red Hat"* ]] || [[ "$OS_NAME" == *"RHEL"* ]]; then
            OS_TYPE="rhel"
        elif [[ "$OS_NAME" == *"Fedora"* ]]; then
            OS_TYPE="fedora"
        else
            OS_TYPE="unknown"
        fi
    else
        OS_TYPE="unknown"
        OS_NAME="غير معروف"
        OS_VERSION="غير معروف"
    fi
}

# التحقق من صلاحيات السودو
check_sudo() {
    echo -e "\nالتحقق من صلاحيات السودو..."
    if ! command -v sudo &> /dev/null; then
        echo "الأمر sudo غير متوفر. يرجى تثبيته أو تشغيل هذا السكريبت بصلاحيات root."
        exit 1
    fi
    
    if [ "$EUID" -ne 0 ]; then
        echo "يرجى تشغيل هذا السكريبت بصلاحيات الجذر (root) باستخدام sudo"
        exit 1
    fi
    
    echo "✅ صلاحيات السودو متوفرة"
}

# تثبيت حزم PHP المطلوبة بناءً على نظام التشغيل
install_php_packages() {
    local php_version="$1"
    local exts=("mysql" "zip" "gd" "mbstring" "xml" "curl" "json" "tokenizer" "fileinfo" "openssl")
    
    echo -e "\nتثبيت امتدادات PHP المطلوبة..."
    
    case $OS_TYPE in
        ubuntu|debian)
            apt-get update
            for ext in "${exts[@]}"; do
                echo "تثبيت php$php_version-$ext..."
                apt-get install -y php$php_version-$ext
            done
            ;;
        
        centos|rhel)
            yum -y install epel-release
            yum -y update
            for ext in "${exts[@]}"; do
                echo "تثبيت php$php_version-$ext..."
                yum -y install php$php_version-$ext
            done
            ;;
        
        fedora)
            dnf -y update
            for ext in "${exts[@]}"; do
                echo "تثبيت php$php_version-$ext..."
                dnf -y install php$php_version-$ext
            done
            ;;
        
        *)
            echo "⚠️ نظام التشغيل غير مدعوم للتثبيت التلقائي ($OS_NAME)."
            echo "يرجى تثبيت الحزم التالية يدوياً:"
            for ext in "${exts[@]}"; do
                echo "- php$php_version-$ext"
            done
            ;;
    esac
}

# تثبيت وإعداد MySQL بناءً على نظام التشغيل
install_mysql() {
    echo -e "\nتثبيت MySQL Server..."
    
    case $OS_TYPE in
        ubuntu|debian)
            apt-get update
            apt-get install -y mysql-server
            systemctl start mysql
            systemctl enable mysql
            ;;
        
        centos|rhel)
            yum -y install mysql-server
            systemctl start mysqld
            systemctl enable mysqld
            ;;
        
        fedora)
            dnf -y install mysql-server
            systemctl start mysqld
            systemctl enable mysqld
            ;;
        
        *)
            echo "⚠️ نظام التشغيل غير مدعوم للتثبيت التلقائي لـ MySQL."
            echo "يرجى تثبيت MySQL يدوياً."
            return 1
            ;;
    esac
    
    # التحقق من أن خدمة MySQL تعمل
    if systemctl is-active --quiet mysql || systemctl is-active --quiet mysqld; then
        echo "✅ تم تثبيت MySQL وتشغيله بنجاح"
        return 0
    else
        echo "⚠️ تم تثبيت MySQL ولكن لم يتم تشغيله. يرجى التحقق من الأخطاء."
        return 1
    fi
}

# إنشاء قاعدة بيانات MySQL
setup_mysql_database() {
    local db_name="$1"
    local db_user="$2"
    local db_pass="$3"
    
    echo -e "\nإنشاء قاعدة بيانات MySQL..."
    
    # التحقق من وجود قاعدة البيانات
    if mysql -e "USE $db_name" 2>/dev/null; then
        echo "✅ قاعدة البيانات $db_name موجودة بالفعل"
    else
        echo "إنشاء قاعدة بيانات $db_name..."
        mysql -e "CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
        
        # إنشاء مستخدم إذا كان غير root
        if [ "$db_user" != "root" ]; then
            echo "إنشاء مستخدم $db_user لقاعدة البيانات..."
            mysql -e "CREATE USER IF NOT EXISTS '$db_user'@'localhost' IDENTIFIED BY '$db_pass';"
            mysql -e "GRANT ALL PRIVILEGES ON $db_name.* TO '$db_user'@'localhost';"
        else
            echo "منح الصلاحيات لـ root على قاعدة البيانات..."
            mysql -e "GRANT ALL PRIVILEGES ON $db_name.* TO 'root'@'localhost';"
        fi
        
        mysql -e "FLUSH PRIVILEGES;"
        echo "✅ تم إنشاء قاعدة البيانات وإعدادها بنجاح"
    fi
}

# إعداد SQLite
setup_sqlite() {
    local db_path="$1"
    
    echo -e "\nإعداد SQLite..."
    
    # التأكد من وجود المجلد
    mkdir -p $(dirname "$db_path")
    
    # إنشاء ملف قاعدة البيانات إذا لم يكن موجوداً
    if [ ! -f "$db_path" ]; then
        touch "$db_path"
        chmod 666 "$db_path"
        echo "✅ تم إنشاء ملف قاعدة بيانات SQLite في $db_path"
    else
        echo "✅ ملف قاعدة بيانات SQLite موجود بالفعل في $db_path"
        # التأكد من الأذونات
        chmod 666 "$db_path"
    fi
}

# تحديث ملف .env
update_env_file() {
    local db_type="$1"
    local db_host="$2"
    local db_port="$3"
    local db_name="$4"
    local db_user="$5"
    local db_pass="$6"
    local db_path="$7"
    
    if [ ! -f .env ] && [ -f .env.example ]; then
        echo -e "\nنسخ ملف .env.example إلى .env..."
        cp .env.example .env
    fi
    
    if [ -f .env ]; then
        echo -e "\nتحديث ملف .env..."
        
        # تحديث إعدادات قاعدة البيانات
        if [ "$db_type" == "sqlite" ]; then
            sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=sqlite/' .env
            sed -i 's/DB_DATABASE=.*/DB_DATABASE='"$db_path"'/' .env
        else
            sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env
            sed -i 's/DB_HOST=.*/DB_HOST='"$db_host"'/' .env
            sed -i 's/DB_PORT=.*/DB_PORT='"$db_port"'/' .env
            sed -i 's/DB_DATABASE=.*/DB_DATABASE='"$db_name"'/' .env
            sed -i 's/DB_USERNAME=.*/DB_USERNAME='"$db_user"'/' .env
            sed -i 's/DB_PASSWORD=.*/DB_PASSWORD='"$db_pass"'/' .env
        fi
        
        echo "✅ تم تحديث ملف .env بنجاح"
    else
        echo "⚠️ ملف .env غير موجود ولم يتم العثور على ملف .env.example"
    fi
}

# التحقق من إصدار PHP
check_php_version() {
    if ! command -v php &> /dev/null; then
        echo "❌ PHP غير مثبت."
        return 1
    fi
    
    PHP_VERSION=$(php -r "echo PHP_VERSION;")
    echo -e "\nإصدار PHP الحالي: $PHP_VERSION"
    
    # التحقق من أن PHP بإصدار 8.2 أو أعلى
    PHP_VALID=$(php -r "echo version_compare(PHP_VERSION, '8.2.0', '>=') ? 'true' : 'false';")
    PHP_RECOMMENDED=$(php -r "echo version_compare(PHP_VERSION, '8.3.0', '>=') ? 'true' : 'false';")
    
    if [ "$PHP_VALID" == "true" ]; then
        if [ "$PHP_RECOMMENDED" == "true" ]; then
            echo "✅ إصدار PHP متوافق ومُوصى به"
        else
            echo "✅ إصدار PHP متوافق، ولكن يُنصح بالترقية إلى PHP 8.3+"
        fi
        PHP_VERSION_MAJOR=$(php -r "echo PHP_MAJOR_VERSION;")
        PHP_VERSION_MINOR=$(php -r "echo PHP_MINOR_VERSION;")
        PHP_VERSION_SHORT="$PHP_VERSION_MAJOR.$PHP_VERSION_MINOR"
        return 0
    else
        echo "❌ إصدار PHP غير متوافق. يجب استخدام PHP 8.2.0 أو أحدث."
        return 1
    fi
}

# استدعاء سكريبت التحقق من المتطلبات
run_php_check() {
    echo -e "\nتشغيل فحص متطلبات PHP..."
    
    if [ -f "check-requirements.php" ]; then
        php check-requirements.php
        return $?
    else
        echo "⚠️ ملف check-requirements.php غير موجود!"
        return 1
    fi
}

# التحقق من وجود حزم مطلوبة
check_required_commands() {
    local commands=("php" "mysql" "composer")
    local missing_commands=()
    
    echo -e "\nالتحقق من وجود الأوامر المطلوبة..."
    
    for cmd in "${commands[@]}"; do
        if ! command -v $cmd &> /dev/null; then
            missing_commands+=("$cmd")
            echo "❌ الأمر $cmd غير متوفر"
        else
            echo "✅ الأمر $cmd متوفر"
        fi
    done
    
    if [ ${#missing_commands[@]} -gt 0 ]; then
        echo -e "\n⚠️ يجب تثبيت الحزم التالية:"
        
        for cmd in "${missing_commands[@]}"; do
            case $cmd in
                php)
                    echo "- PHP يجب تثبيته قبل المتابعة"
                    echo "  يرجى تثبيت PHP 8.2+ باستخدام مدير الحزم الخاص بنظام التشغيل"
                    ;;
                
                mysql)
                    echo "- MySQL/MariaDB (مطلوب فقط إذا كنت تستخدم MySQL ولا تستخدم SQLite)"
                    ;;
                
                composer)
                    echo "- Composer (مدير الحزم لـ PHP)"
                    echo "  يرجى تثبيت Composer باستخدام:"
                    echo "  curl -sS https://getcomposer.org/installer | php"
                    echo "  sudo mv composer.phar /usr/local/bin/composer"
                    ;;
            esac
        done
        
        return 1
    fi
    
    return 0
}

# الوظيفة الرئيسية
main() {
    # تحديد نظام التشغيل
    detect_os
    echo -e "\nنظام التشغيل المكتشف: $OS_NAME $OS_VERSION ($OS_TYPE)"
    
    # التحقق من صلاحيات السودو
    check_sudo
    
    # التحقق من وجود الأوامر المطلوبة
    check_required_commands || {
        echo "❌ الرجاء تثبيت الحزم المطلوبة قبل المتابعة."
        exit 1
    }
    
    # التحقق من إصدار PHP
    check_php_version || {
        echo "❌ الرجاء تثبيت PHP بإصدار 8.2.0 أو أحدث قبل المتابعة."
        exit 1
    }
    
    # تحديد نوع قاعدة البيانات
    DB_CHOICE="mysql"
    if grep -q "DB_CONNECTION=sqlite" .env 2>/dev/null; then
        DB_CHOICE="sqlite"
        echo -e "\nتم اكتشاف SQLite كقاعدة بيانات في ملف .env."
    fi
    
    read -p "هل ترغب في استخدام SQLite (s) أو MySQL (m)؟ [s/m]: " DB_TYPE
    if [[ $DB_TYPE =~ ^[Ss]$ ]]; then
        DB_CHOICE="sqlite"
    else
        DB_CHOICE="mysql"
    fi
    
    # تثبيت امتدادات PHP المطلوبة
    install_php_packages "$PHP_VERSION_SHORT"
    
    # إعداد قاعدة البيانات
    DB_NAME="jak_travel_sys"
    DB_HOST="127.0.0.1"
    DB_PORT="3306"
    DB_USER="root"
    DB_PASS=""
    DB_PATH="database/database.sqlite"
    
    if [ "$DB_CHOICE" == "mysql" ]; then
        # تثبيت MySQL إذا لم تكن موجودة
        if ! command -v mysql &> /dev/null; then
            install_mysql || {
                echo "⚠️ تم مواجهة مشكلة في تثبيت MySQL."
                echo "هل ترغب في استخدام SQLite بدلاً من ذلك؟ [y/n]: "
                read USE_SQLITE_INSTEAD
                if [[ $USE_SQLITE_INSTEAD =~ ^[Yy]$ ]]; then
                    DB_CHOICE="sqlite"
                else
                    echo "❌ يرجى تثبيت MySQL يدوياً ثم تشغيل السكريبت مرة أخرى."
                    exit 1
                fi
            }
        fi
        
        if [ "$DB_CHOICE" == "mysql" ]; then
            # السؤال عن تفاصيل MySQL
            read -p "اسم قاعدة البيانات [jak_travel_sys]: " DB_NAME_INPUT
            DB_NAME=${DB_NAME_INPUT:-$DB_NAME}
            
            read -p "اسم المستخدم [root]: " DB_USER_INPUT
            DB_USER=${DB_USER_INPUT:-$DB_USER}
            
            read -p "كلمة المرور []: " DB_PASS_INPUT
            DB_PASS=${DB_PASS_INPUT:-$DB_PASS}
            
            # إعداد قاعدة بيانات MySQL
            setup_mysql_database "$DB_NAME" "$DB_USER" "$DB_PASS" || {
                echo "⚠️ تم مواجهة مشكلة في إعداد قاعدة بيانات MySQL."
                exit 1
            }
            
            # تحديث ملف .env
            update_env_file "mysql" "$DB_HOST" "$DB_PORT" "$DB_NAME" "$DB_USER" "$DB_PASS" ""
        fi
    fi
    
    if [ "$DB_CHOICE" == "sqlite" ]; then
        # التأكد من المسار المطلوب لملف SQLite
        read -p "مسار قاعدة بيانات SQLite [database/database.sqlite]: " DB_PATH_INPUT
        DB_PATH=${DB_PATH_INPUT:-$DB_PATH}
        
        # إعداد قاعدة بيانات SQLite
        setup_sqlite "$DB_PATH" || {
            echo "⚠️ تم مواجهة مشكلة في إعداد قاعدة بيانات SQLite."
            exit 1
        }
        
        # تحديث ملف .env
        update_env_file "sqlite" "" "" "" "" "" "$DB_PATH"
    fi
    
    # تشغيل فحص المتطلبات
    echo -e "\nجاري التحقق من توافق النظام بعد التثبيت..."
    run_php_check
    
    echo -e "\n=== اكتمل تثبيت المتطلبات ==="
    echo "الآن قم بتنفيذ الخطوات التالية:"
    echo "1. قم بتشغيل: composer install"
    echo "2. قم بتشغيل: php artisan key:generate"
    echo "3. قم بتشغيل: php artisan migrate"
    echo "4. قم بتشغيل: php artisan db:seed (اختياري: لإضافة بيانات تجريبية)"
    echo "5. قم بتشغيل: php artisan app:setup-admin-user (لإنشاء حساب المدير)"
    
    echo -e "\nلتشغيل التطبيق، استخدم الأمر التالي:"
    echo "php artisan serve"
}

# بدء تنفيذ السكريبت
main