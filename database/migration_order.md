# ترتيب تنفيذ ملفات الهجرة

## المجموعة 1: الجداول الأساسية
1. 0000_12_31_000001_create_agencies_table.php
2. 0001_01_01_000000_create_users_table.php
3. 0001_01_01_000001_create_cache_table.php
4. 0001_01_01_000002_create_jobs_table.php
5. 2023_01_01_000003_create_services_table.php
6. 2023_01_01_000004_create_service_subagent_table.php
7. 2023_01_01_000005_create_requests_table.php
8. 2023_01_01_000006_create_quotes_table.php
9. 2023_01_01_000007_create_transactions_table.php
10. 2023_01_01_000008_create_documents_table.php
11. 2024_04_07_000001_create_currencies_table.php
12. 2024_04_15_000001_create_quote_attachments_table.php
13. 2024_04_16_000002_create_notifications_table.php
14. 2024_05_15_000001_create_payments_table.php
15. 2025_04_06_190353_create_personal_access_tokens_table.php

- جميع الملفات أعلاه هي ملفات الإنشاء الأساسية فقط.
- لا يوجد أي ملفات تعديل أو تكرار غير ضروري.
- ترتيب التنفيذ يراعي الاعتماد بين الجداول (users → agencies → ...).
- إذا أضفت جدول جديد، أنشئ له ملف واحد فقط وحدث هذا التوثيق.

# ترتيب ملفات التعديلات (migrations) للجداول الأساسية حسب الاعتماد

## 1. currencies (العملات)
- 2024_04_07_000001_create_currencies_table.php
- 2025_04_11_174037_add_missing_columns_to_tables.php
- 2025_04_11_184724_add_unique_constraint_to_currencies_table.php: Adds a unique constraint to the currencies table to ensure no duplicate entries.

## 2. users (المستخدمون)
- 0001_01_01_000000_create_users_table.php
- 2024_04_15_000002_add_profile_fields_to_users_table.php
- 2025_04_10_184958_add_user_preferences_to_users_table.php
- 2025_04_11_170637_add_role_column_to_users_table.php
- 2025_04_23_204240_add_status_to_users_table.php
- 2025_04_24_224939_add_theme_to_users_table.php
- 2025_04_24_230710_add_email_notifications_to_users_table.php
- 2025_04_25_173209_add_user_type_to_users_table.php
- 2025_04_25_173602_add_is_active_to_users_table.php
- 2025_04_28_204757_add_phone_to_users_table.php
- 2025_05_01_000001_add_admin_to_user_types.php

## 3. agencies (الوكالات)
- 0000_12_31_000001_create_agencies_table.php
- 2024_04_15_000001_update_agencies_table_add_settings.php
- 2024_04_16_000003_add_contact_email_to_agencies_table.php
- 2024_04_16_000004_add_settings_columns_to_agencies_table.php
- 2024_05_01_000001_add_commission_rate_to_agencies_table.php
- 2025_04_11_183450_add_website_column_to_agencies_table.php
- 2025_04_26_000001_add_user_id_to_agencies_table.php

## 4. services (الخدمات)
- 2023_01_01_000003_create_services_table.php
- 2025_04_11_184200_add_currency_id_column_to_services_table.php
- 2025_04_11_190029_add_image_column_to_services_table.php
- 2025_04_11_174037_add_missing_columns_to_tables.php
- 2025_05_01_000004_update_services_agency_id_default.php

## 5. service_subagent (علاقة الخدمات بالوكلاء الفرعيين)
- 2023_01_01_000004_create_service_subagent_table.php

## 6. requests (الطلبات)
- 2023_01_01_000005_create_requests_table.php
- 2024_04_16_000005_rename_requests_table.php
- 2024_04_30_000001_rename_requests_table.php

## 7. quotes (العروض)
- 2023_01_01_000006_create_quotes_table.php
- 2025_04_11_191937_create_quotes_table.php
- 2025_04_11_192317_add_missing_columns_to_quotes_table.php
- 2025_04_11_194749_make_subagent_id_nullable_in_quotes_table.php
- 2025_04_11_195757_make_commission_amount_nullable_in_quotes_table.php
- 2024_04_16_000001_add_rejection_reason_to_quotes_table.php

## 8. transactions (المعاملات)
- 2023_01_01_000007_create_transactions_table.php
- 2025_04_11_205228_add_currency_id_to_transactions_table.php
- 2025_05_01_000002_add_missing_columns_to_transactions_table.php
- 2025_05_01_000002_update_transactions_table_structure.php
- 2025_05_01_000003_update_transaction_type_column.php

## 9. payments (المدفوعات)
- 2024_05_15_000001_create_payments_table.php
- 2025_04_11_205144_add_completed_at_to_payments_table.php

## 10. documents (الوثائق)
- 2023_01_01_000008_create_documents_table.php
- 2025_04_12_043151_add_size_to_documents_table.php
- 2025_04_12_044641_add_uploaded_by_to_documents_table.php
- 2025_04_12_045301_modify_user_id_in_documents_table.php

## 11. quote_attachments (مرفقات العروض)
- 2024_04_15_000001_create_quote_attachments_table.php
- 2025_04_12_044818_create_quote_attachments_table.php
- 2025_04_12_045531_add_name_to_quote_attachments_table.php

## 12. notifications (الإشعارات)
- 2024_04_10_000001_create_notifications_table.php
- 2024_04_10_000002_create_notifications_table.php
- 2025_04_12_045735_fix_notifications_columns_structure.php
- 2025_04_12_050712_recreate_notifications_table.php
- 2025_04_12_051451_fix_notifications_table_for_tests.php
- 2025_04_12_074800_add_user_id_to_notifications_table.php
- 2025_04_12_100000_standardize_notifications_table.php
- 2025_04_22_000001_remove_user_id_from_notifications_table.php
- 2025_04_28_183838_add_is_read_to_notifications_table.php
- 2025_05_01_000003_add_message_to_notifications_table.php
- 2025_05_01_000010_update_notifications_table_structure.php
- 2025_05_01_000020_fix_notifications_table.php
