# ترتيب تنفيذ ملفات الهجرة

## المجموعة 1: الجداول الأساسية
1. 0001_01_01_000000_create_users_table.php
2. 0001_01_01_000001_create_cache_table.php
3. 0001_01_01_000002_create_jobs_table.php
4. 2023_01_01_000001_create_agencies_table.php
5. 2024_04_07_000001_create_currencies_table.php

## المجموعة 2: الجداول الثانوية
1. 2023_01_01_000003_create_services_table.php
2. 2023_01_01_000004_create_service_subagent_table.php
3. 2023_01_01_000005_create_requests_table.php (ملاحظة: تم إعادة تسميتها لاحقاً)
4. 2023_01_01_000006_create_quotes_table.php

## المجموعة 3: الجداول المرتبطة
1. 2023_01_01_000007_create_transactions_table.php
2. 2023_01_01_000008_create_documents_table.php
3. 2024_05_15_000001_create_payments_table.php
4. 2024_04_16_000002_create_notifications_table.php

## المجموعة 4: التعديلات على الجداول
1. 2024_04_15_000001_create_quote_attachments_table.php
2. 2024_04_15_000001_update_agencies_table_add_settings.php
3. 2024_04_15_000002_add_profile_fields_to_users_table.php
4. 2024_04_16_000001_add_rejection_reason_to_quotes_table.php
5. 2024_04_16_000003_add_contact_email_to_agencies_table.php
6. 2024_04_16_000004_add_settings_columns_to_agencies_table.php
7. 2024_04_16_000005_rename_requests_table.php
8. 2024_04_30_000001_rename_requests_table.php
9. 2024_05_01_000001_add_commission_rate_to_agencies_table.php
10. 2024_05_10_000001_add_locale_to_users_table.php

# Migration Order

To ensure migrations are executed in the correct order, follow this sequence:

1. 2023_01_01_000005_create_requests_table.php
2. 2025_04_11_195757_make_commission_amount_nullable_in_quotes_table.php

Ensure that all migrations are listed in the correct order to avoid dependency issues.
