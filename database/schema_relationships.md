# العلاقات بين جداول قاعدة البيانات (ERD) - تحديث 2025-05-03

## الجداول الأساسية

### agencies (الوكالات)
- id (PK)
- user_id (FK → users.id, nullable) - Represents the primary user or administrator associated with the agency.
- name
- logo (nullable)
- phone
- contact_email (nullable)
- email (unique)
- address (nullable)
- status (default 'active')
- notification_settings (json, nullable)
- email_settings (json, nullable)
- commission_settings (json, nullable)
- price_decimals (default 2)
- price_display_format (default 'symbol_first')
- auto_convert_prices (default true)
- default_commission_rate (default 10.00)
- default_currency (default 'SAR')
- website (nullable)
- timestamps

### users (المستخدمون)
- id (PK)
- agency_id (FK → agencies.id, nullable)
- name
- email (unique)
- email_verified_at (nullable)
- password
- role (default 'customer')
- status (default 'active')
- avatar (nullable)
- id_number (nullable)
- passport_number (nullable)
- nationality (nullable)
- city (nullable)
- country (nullable)
- preferred_currency (nullable)
- notification_preferences (json, nullable)
- locale (default 'ar')
- theme (nullable)
- email_notifications (default true)
- phone (nullable)
- is_active (default true)
- is_admin (default false)
- user_type (nullable)
- remember_token
- timestamps

### services (الخدمات)
- id (PK)
- agency_id (FK → agencies.id, nullable)
- name
- description (nullable)
- type (enum)
- status (enum, default 'active')
- base_price (default 0)
- commission_rate (default 0)
- currency_id (FK → currencies.id, nullable)
- image (nullable)
- timestamps

### service_subagent (علاقة الخدمات بالوكلاء الفرعيين)
- id (PK)
- service_id (FK → services.id)
- subagent_id (FK → users.id)
- custom_commission_rate (nullable)
- is_active (default true)
- timestamps

### requests (الطلبات)
- id (PK)
- user_id (FK → users.id)
- agency_id (FK → agencies.id)
- service_id (FK → services.id)
- ...
- timestamps

### quotes (العروض)
- id (PK)
- request_id (FK → requests.id)
- user_id (FK → users.id, nullable)
- currency_id (FK → currencies.id, nullable)
- description (nullable)
- valid_until (nullable)
- notes (nullable)
- rejection_reason (nullable)
- subagent_id (FK → users.id, nullable)
- price
- commission_amount (nullable)
- details (nullable)
- status (enum)
- timestamps

### transactions (المعاملات)
- id (PK)
- agency_id (FK → agencies.id, nullable)
- user_id (FK → users.id)
- quote_id (FK → quotes.id, nullable)
- currency_id (FK → currencies.id, nullable)
- reference_id (nullable)
- payment_method (nullable)
- description (nullable)
- refunded_at (nullable)
- refund_reason (nullable)
- refund_reference (nullable)
- amount
- type (enum)
- status (enum)
- notes (nullable)
- timestamps

### payments (المدفوعات)
- id (PK)
- payment_id (uuid, unique)
- quote_id (FK → quotes.id)
- user_id (FK → users.id)
- amount
- currency_code
- payment_method
- status
- transaction_id (nullable)
- error_message (nullable)
- payment_details (nullable)
- completed_at (nullable)
- timestamps

### documents (الوثائق)
- id (PK)
- name
- file_path
- file_type (nullable)
- size (nullable)
- documentable_id/documentable_type (morphs)
- uploaded_by (FK → users.id)
- visibility (enum)
- notes (nullable)
- timestamps

### quote_attachments (مرفقات العروض)
- id (PK)
- quote_id (FK → quotes.id)
- file_name (nullable)
- file_path (nullable)
- file_type (nullable)
- file_size (default 0)
- description (nullable)
- uploaded_by (FK → users.id, nullable)
- timestamps

### notifications (الإشعارات)
- id (uuid, PK)
- type
- notifiable_id/notifiable_type (morphs)
- data (text)
- message (nullable)
- title (nullable)
- is_read (default false)
- user_id (FK → users.id, nullable)
- read_at (nullable)
- timestamps

### currencies (العملات)
- id (PK)
- code (unique)
- name
- symbol
- symbol_position (default 'before')
- is_default (default false)
- exchange_rate (default 1.0000)
- status (default 'active')
- timestamps

---

## العلاقات الأساسية (Foreign Keys)
- users.agency_id → agencies.id
- services.agency_id → agencies.id
- services.currency_id → currencies.id
- service_subagent.service_id → services.id
- service_subagent.subagent_id → users.id
- requests.user_id → users.id
- requests.agency_id → agencies.id
- requests.service_id → services.id
- quotes.request_id → requests.id
- quotes.user_id → users.id
- quotes.currency_id → currencies.id
- quotes.subagent_id → users.id
- transactions.agency_id → agencies.id
- transactions.user_id → users.id
- transactions.quote_id → quotes.id
- transactions.currency_id → currencies.id
- payments.user_id → users.id
- payments.quote_id → quotes.id
- documents.uploaded_by → users.id
- quote_attachments.quote_id → quotes.id
- quote_attachments.uploaded_by → users.id
- notifications.user_id → users.id

---

## ملاحظات:
- جميع الجداول الأساسية أصبحت تحتوي على جميع الأعمدة المطلوبة في ملف الإنشاء الأساسي فقط.
- تم حذف جميع ملفات التعديلات الإضافية (migrations) الخاصة بالأعمدة الثانوية.
- جميع العلاقات بين الجداول موثقة أعلاه ويمكن الاعتماد عليها في أي تطوير أو مراجعة مستقبلية.
