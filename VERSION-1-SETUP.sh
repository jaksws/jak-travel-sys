#!/bin/bash

echo "إعداد النسخة 1.0 من نظام وكالات السفر RTLA"
echo "============================================"

# تأكيد أننا في المجلد الصحيح
if [ ! -f "artisan" ]; then
    echo "خطأ: يجب تشغيل هذا السكريبت من مجلد Laravel الرئيسي"
    exit 1
fi

# التأكد من تحديث الفرع الرئيسي
echo "جاري تحديث الفرع الرئيسي..."
git checkout main
git pull origin main

# إنشاء فرع للنسخة 1.0
echo "جاري إنشاء فرع للنسخة 1.0..."
git checkout -b version-1.0

# تحديث ملف composer.json للنسخة 1.0
echo "تحديث معلومات الإصدار..."
sed -i 's/"version": "1.0.0"/"version": "1.0.0"/g' composer.json || echo "خطأ في تحديث ملف composer.json"

# إنشاء ملف تكوين لميزات النسخة 1.0
echo "إنشاء ملف تكوين للنسخة 1.0..."
cat > config/v1_features.php << 'EOL'
<?php

return [
    'multilingual' => [
        'enabled' => true,
        'available_locales' => ['ar', 'en', 'fr', 'tr', 'es', 'id', 'ur'],
        'default_locale' => 'ar',
    ],
    'dark_mode' => [
        'enabled' => true,
        'default' => 'system',
    ],
    'payment_system' => [
        'enabled' => true,
        'providers' => [
            'mada' => [
                'enabled' => true,
                'test_mode' => true,
            ],
            'visa' => [
                'enabled' => true,
                'test_mode' => true,
            ],
            'mastercard' => [
                'enabled' => true,
                'test_mode' => true,
            ],
            'apple_pay' => [
                'enabled' => true,
                'test_mode' => true,
            ],
            'google_pay' => [
                'enabled' => true,
                'test_mode' => true,
            ],
        ],
    ],
    'enhanced_ui' => [
        'enabled' => true,
    ],
    'analytics' => [
        'enabled' => true,
        'modules' => [
            'dashboard' => true,
            'reports' => true,
            'customer_insights' => true,
            'market_trends' => true,
        ],
    ],
    'additional_languages' => [
        'enabled' => true,
        'languages' => [
            'es' => true,
            'id' => true,
            'ur' => true,
        ],
    ],
];
EOL

# إضافة الملفات الجديدة للنسخة 1.0
echo "إضافة التغييرات إلى Git..."
git add .
git commit -m "بدء النسخة 1.0: إعداد الفرع وملفات التكوين الأساسية"

# طباعة إرشادات المتابعة
echo ""
echo "تم إعداد فرع النسخة 1.0 بنجاح!"
echo ""
echo "للبدء في تطوير النسخة 1.0، يمكنك البدء بتنفيذ المهام التالية:"
echo "1. راجع خريطة الطريق في ملف VERSION-2-ROADMAP.md"
echo "2. ابدأ بتنفيذ البنية التحتية للميزات ذات الأولوية العالية"
echo "3. قم بتحديث ملف التكوين config/v1_features.php عند إضافة ميزات جديدة"
echo ""
echo "لدفع التغييرات إلى المستودع البعيد:"
echo "git push -u origin version-1.0"
echo ""
echo "لاختبار ميزات النسخة 1.0، تأكد من تشغيل المهاجرات الجديدة:"
echo "php artisan migrate"

# سكريبت ترقية نظام وكالات السفر (RTLA) من الإصدار 1.0 إلى 1.0
# الاستخدام: ./VERSION-1-SETUP.sh

# تعيين الألوان للإخراج
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${YELLOW}بدء عملية الترقية إلى النسخة 1.0 من نظام وكالات السفر (RTLA)${NC}\n"

# التأكد من أننا في المجلد الصحيح
if [ ! -f "artisan" ]; then
    echo -e "${RED}خطأ: لم يتم العثور على ملف artisan. يرجى التأكد من تشغيل السكريبت من المجلد الرئيسي للمشروع.${NC}"
    exit 1
fi

# التحقق من إصدار النظام الحالي
echo -e "${YELLOW}التحقق من الإصدار الحالي...${NC}"
CURRENT_VERSION=$(grep "version" composer.json | head -1 | awk -F: '{ print $2 }' | sed 's/[",]//g' | tr -d '[:space:]')

if [[ "$CURRENT_VERSION" == "1."* ]]; then
    echo -e "${YELLOW}ملاحظة: يبدو أنك تستخدم بالفعل الإصدار $CURRENT_VERSION${NC}"
    read -p "هل ترغب في المتابعة على أي حال؟ (y/n): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo -e "${YELLOW}تم إلغاء الترقية.${NC}"
        exit 0
    fi
fi

# إنشاء نسخة احتياطية قبل الترقية
echo -e "${YELLOW}إنشاء نسخة احتياطية من الملفات والبيانات...${NC}"
BACKUP_DIR="backups/pre_upgrade_v1_$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"

# نسخة احتياطية من ملفات التكوين
cp .env "$BACKUP_DIR/.env" 2>/dev/null || echo -e "${YELLOW}لا يمكن نسخ ملف .env${NC}"
cp -r config "$BACKUP_DIR/config" 2>/dev/null || echo -e "${YELLOW}لا يمكن نسخ مجلد config${NC}"

# نسخة احتياطية من قاعدة البيانات إذا كانت SQLite
if grep -q "DB_CONNECTION=sqlite" .env; then
    cp database/*.sqlite "$BACKUP_DIR/" 2>/dev/null || echo -e "${YELLOW}لا يمكن نسخ ملفات قاعدة البيانات SQLite${NC}"
else
    # إذا كان هناك حاجة، أضف هنا أوامر نسخ احتياطي لأنواع أخرى من قواعد البيانات
    echo -e "${YELLOW}لإنشاء نسخة احتياطية من قاعدة البيانات الخاصة بك، يرجى الرجوع إلى دليل المستخدم${NC}"
fi

echo -e "${GREEN}تم إنشاء النسخة الاحتياطية في مجلد: $BACKUP_DIR${NC}"

# تحديث ملفات المشروع
echo -e "${YELLOW}تحديث ملفات المشروع...${NC}"

# التحقق من وجود git
if command -v git &> /dev/null; then
    git fetch origin || echo -e "${YELLOW}لا يمكن جلب التحديثات من المستودع البعيد${NC}"
    git checkout tags/v1.0.0 -b upgrade-v1.0.0 || echo -e "${YELLOW}لا يمكن الانتقال إلى الإصدار 1.0.0${NC}"
else
    echo -e "${YELLOW}لم يتم العثور على git. سيتم تخطي تحديث الملفات عبر git.${NC}"
    echo -e "${YELLOW}يرجى تحميل الإصدار 1.0.0 يدويًا وتحديث الملفات.${NC}"
fi

# إنشاء ملف تكوين ميزات الإصدار 1.0
echo -e "${YELLOW}إنشاء ملف تكوين ميزات الإصدار 1.0...${NC}"
mkdir -p config
cat > config/v1_features.php << 'EOL'
<?php

return [
    'multilingual' => [
        'enabled' => true,
        'available_locales' => ['ar', 'en', 'fr', 'tr', 'es', 'id', 'ur'],
        'default_locale' => 'ar',
    ],
    'dark_mode' => [
        'enabled' => true,
        'default' => 'system',
    ],
    'payment_system' => [
        'enabled' => true,
        'providers' => [
            'mada' => [
                'enabled' => true,
                'test_mode' => true,
            ],
            'visa' => [
                'enabled' => true,
                'test_mode' => true,
            ],
            'mastercard' => [
                'enabled' => true,
                'test_mode' => true,
            ],
            'apple_pay' => [
                'enabled' => true,
                'test_mode' => true,
            ],
            'google_pay' => [
                'enabled' => true,
                'test_mode' => true,
            ],
        ],
    ],
    'enhanced_ui' => [
        'enabled' => true,
    ],
    'analytics' => [
        'enabled' => true,
        'modules' => [
            'dashboard' => true,
            'reports' => true,
            'customer_insights' => true,
            'market_trends' => true,
        ],
    ],
    'additional_languages' => [
        'enabled' => true,
        'languages' => [
            'es' => true,
            'id' => true,
            'ur' => true,
        ],
    ],
];
EOL

echo -e "${GREEN}تم إنشاء ملف تكوين ميزات الإصدار 1.0${NC}"

# تحديث التبعيات
echo -e "${YELLOW}تحديث تبعيات Composer...${NC}"
composer install --no-interaction || echo -e "${RED}فشل تحديث تبعيات Composer${NC}"

echo -e "${YELLOW}تحديث تبعيات NPM...${NC}"
npm install || echo -e "${RED}فشل تحديث تبعيات NPM${NC}"

# تنفيذ تحديثات إضافية
echo -e "${YELLOW}تحديث ملفات الترجمة...${NC}"
mkdir -p lang/en lang/fr lang/tr

# إضافة إرشادات ختامية
echo -e "\n${GREEN}=============================${NC}"
echo -e "${GREEN}اكتملت عملية الترقية إلى الإصدار 1.0${NC}"
echo -e "${GREEN}=============================${NC}"
echo -e "\n${YELLOW}الخطوات التالية:${NC}"
echo -e "1. قم بتشغيل المايغريشن: ${GREEN}php artisan migrate${NC}"
echo -e "2. قم بتجميع الأصول الأمامية: ${GREEN}npm run build${NC}"
echo -e "3. قم بمسح ذاكرة التخزين المؤقت: ${GREEN}php artisan optimize:clear${NC}"
echo -e "4. قم بتفعيل الميزات الجديدة في ملف: ${GREEN}config/v1_features.php${NC}"
echo -e "\n${YELLOW}ملاحظة: تم إنشاء نسخة احتياطية قبل الترقية في مجلد:${NC} ${GREEN}$BACKUP_DIR${NC}"
echo -e "${YELLOW}إذا واجهتك أي مشكلة، يمكنك استعادة النسخة الاحتياطية.${NC}"

exit 0
