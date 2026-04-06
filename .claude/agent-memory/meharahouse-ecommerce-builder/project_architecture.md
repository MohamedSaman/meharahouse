---
name: Meharahouse Project Architecture
description: Core tech stack, module structure, routes, design system, currency, database schema, and business context for the Meharahouse e-commerce platform
type: project
---

## Tech Stack
- **Framework**: Laravel 13 (PHP 8.3+)
- **Frontend**: Tailwind CSS v4 (via @tailwindcss/vite), Alpine.js v3 (CDN), Livewire v4
- **Fonts**: Google Fonts — Inter (body), Poppins (headings) — loaded in each layout
- **Build tool**: Vite 8 with laravel-vite-plugin
- **Database**: MySQL — database name `meharahouse_db`, local XAMPP defaults (root, no password)

## Three-Module Architecture

### Webpage (Public Storefront)
- Routes prefix: `/` with name prefix `webpage.`
- Livewire components: `app/Livewire/Webpage/`
- Views: `resources/views/livewire/webpage/`
- Layout: `resources/views/layouts/webpage.blade.php`
- Color theme: Deep Navy (#0F172A) + Warm Gold (#D4A017 / #B8860B) on white/cream — redesigned April 2025
- Brand identity: "Mehra House" — sells ONLY abaya dresses and innerwear for women
- Tagline: "Elegance in Every Thread"
- Currency: ETB (Ethiopian Birr) — replace any old "Rs." references
- Announcement bar: gold (#D4A017) background, white text — not dark navy
- Navbar accent color: #D4A017 (not #F59E0B amber)
- Logo: "MH" monogram in gold on dark navy box (no image file — text-based)
- Footer: 4-column (Brand + Social, Quick Links, Collections, Contact) — newsletter strip above columns
- Homepage sections: Hero (gradient navy-to-purple, no images) > Trust Strip > Categories > Featured Products > Why Choose Us > Testimonials > Newsletter CTA

### Admin Panel
- Routes prefix: `admin/` with name prefix `admin.`
- Middleware: `auth` + `admin` (AdminMiddleware checks role === 'admin')
- Livewire components: `app/Livewire/Admin/`
- Views: `resources/views/livewire/admin/`
- Layout: `resources/views/layouts/admin.blade.php`

### Staff Panel
- Routes prefix: `staff/` with name prefix `staff.`
- Middleware: `auth` + `staff` (StaffMiddleware allows admin OR staff)
- Livewire components: `app/Livewire/Staff/`
- Views: `resources/views/livewire/staff/`
- Layout: `resources/views/layouts/staff.blade.php`

## Authentication
- Routes: `auth.login`, `auth.register`, `auth.logout`
- Login component: `App\Livewire\Auth\Login` (redirects admin→/admin, staff→/staff, customer→/)
- Register: creates customers only (role='customer')
- Middleware registered as named aliases in `bootstrap/app.php`

## Route Naming Convention
- Auth: `auth.login`, `auth.register`, `auth.logout`
- Webpage: `webpage.home`, `webpage.shop`, `webpage.product-details`, `webpage.cart`, `webpage.checkout`, `webpage.orders`, `webpage.about`, `webpage.contact`, `webpage.reviews`
- Admin: `admin.dashboard`, `admin.orders`, `admin.whatsapp-orders`, `admin.products`, `admin.categories`, `admin.customers`, `admin.payments`, `admin.payment-integration`, `admin.reports`, `admin.manual-order`, `admin.suppliers`, `admin.purchasing`, `admin.website-settings`, `admin.supplier-payments`, `admin.customer-payments`
- Public (no auth): `whatsapp.order.form`
- Staff: `staff.dashboard`, `staff.orders`, `staff.customers`

## Database Schema (All Migrations Run)
- **users**: id, name, email, password, role (admin/staff/customer), phone, address, avatar
- **categories**: id, parent_id, name, slug, description, image, is_active, sort_order
- **products**: id, category_id, name, slug, description, sku, price, sale_price, stock, images (JSON), is_featured, is_active
- **orders**: id, user_id, order_number, status (pending/processing/shipped/delivered/cancelled), subtotal, tax, shipping_cost, discount, total, shipping_address (JSON), payment_method, payment_status, coupon_code, notes
- **order_items**: id, order_id, product_id, product_name, price, quantity, subtotal
- **carts**: id, user_id (nullable), session_id, product_id, quantity — unique(user_id, product_id)
- **wishlists**: id, user_id, product_id — unique(user_id, product_id)
- **reviews**: id, user_id, product_id, rating (1-5), comment, is_approved — unique(user_id, product_id)
- **coupons**: id, code, type (percent/fixed), value, min_order, usage_limit, used_count, expires_at, is_active
- **settings**: id, key, value
- **banners**: id, title, subtitle, image, link, button_text, is_active, sort_order
- **suppliers**: id, name, contact_person, email, phone, whatsapp, address, city, country, website, notes, is_active
- **purchase_orders**: id, supplier_id (FK), po_number (unique), status (draft/ordered/partial/received/cancelled), subtotal, shipping_cost, total, currency (Rs.), notes, expected_date, ordered_at, received_at
- **purchase_order_items**: id, purchase_order_id (FK cascade), product_id (FK nullable), product_name, sku, quantity_ordered, quantity_received, unit_cost, subtotal

## Seeded Demo Data
- Admin: admin@meharahouse.com / password
- Staff: staff@meharahouse.com / password
- Customers: abebe@example.com, tigist@example.com, solomon@example.com, marta@example.com, dawit@example.com (all password: password)
- 10 categories, 30 products, 4 banners, 4 coupons (WELCOME10, SAVE50, MEHAR20, FREESHIP), 16 settings, 20 sample orders

## Payment Management Module (Added April 2026)

### New Database Tables
- **supplier_invoices**: id, supplier_id (FK suppliers), invoice_number (unique), invoice_date, total_amount, paid_amount, due_amount, status (pending/partial/paid), notes
- **supplier_payment_records**: id, supplier_invoice_id (FK), amount, payment_method (cash/bank_transfer/cheque/mobile_money), reference, paid_at, notes
- **customer_accounts**: id, order_id (nullable FK orders nullOnDelete), customer_name, customer_phone, customer_email, total_amount, paid_amount, due_amount, status (pending/partial/paid), notes
- **customer_payment_records**: id, customer_account_id (FK), amount, payment_type (advance/payment), payment_method (cash/bank_transfer/mobile_money/telebirr/cbebirr), reference, paid_at, notes

### New Models
- `App\Models\SupplierInvoice` — has `recalculate()` method, scopes: pending/partial/paid, statusColor()/statusLabel()
- `App\Models\SupplierPaymentRecord` — belongs to SupplierInvoice, has methodLabel()
- `App\Models\CustomerAccount` — has `recalculate()` method, same scopes/helpers
- `App\Models\CustomerPaymentRecord` — belongs to CustomerAccount, has typeLabel()/methodLabel()

### New Livewire Components
- `App\Livewire\Admin\SupplierPayments` — routes to `admin.supplier-payments` (/admin/supplier-payments)
- `App\Livewire\Admin\CustomerPayments` — routes to `admin.customer-payments` (/admin/customer-payments)

### Sidebar
- New "Payment Management" section added after "Purchasing" section in admin layout
- Supplier Payments link shows badge count of pending+partial invoices
- Customer Payments link shows badge count of pending+partial accounts

## Order Management System (Added April 2026)

### Two Customer Types
1. **Website Orders** (pre-order): Customer orders → pays advance % → uploads receipt → admin confirms → sources/dispatches → customer pays balance
2. **WhatsApp Orders**: Admin generates one-time token link → sends to customer via WA → customer opens link, sees products, uploads receipt, fills address → order created labeled 'whatsapp'

### New Database Tables (migration prefix 2024_01_07)
- **orders** (altered): added `source` ENUM(website,whatsapp), `advance_percentage` TINYINT, `advance_amount` DECIMAL, `balance_amount` DECIMAL, `supplier_status` ENUM(none,ordered,received,unavailable), `refund_option` ENUM(refund,reorder) nullable
- **orders** status column: changed to ENUM(new,payment_received,confirmed,sourcing,dispatched,delivered,completed,refunded,cancelled) — old statuses migrated: pending→new, processing→confirmed, shipped→dispatched
- **order_payments**: id, order_id FK, type ENUM(advance,balance,refund), amount, method ENUM(bank_transfer,online,cash), receipt_path nullable, reference nullable, status ENUM(pending,confirmed,rejected), confirmed_by FK users nullable, confirmed_at nullable, notes
- **order_status_logs**: id, order_id FK cascade, from_status nullable, to_status, notes, created_by FK users nullable
- **whatsapp_order_tokens**: id, token VARCHAR(64) unique, created_by FK users, products JSON, subtotal, advance_percentage, advance_amount, expires_at nullable, used_at nullable, order_id FK nullable, status ENUM(pending,used,expired), notes
- **refunds**: id, order_id FK, amount, method ENUM(bank_transfer,online), reference nullable, notes, processed_by FK nullable, processed_at nullable

### New Models
- `App\Models\OrderPayment` — scopes: pending(), confirmed(), advance(), balance(); receiptUrl() returns asset URL
- `App\Models\OrderStatusLog` — belongs to Order, belongs to User (createdBy)
- `App\Models\WhatsappOrderToken` — isUsable(), markUsed($orderId), static generate(...)
- `App\Models\Refund` — belongs to Order, belongs to User (processedBy)
- `App\Models\Order` updated — added: payments(), statusLogs(), whatsappToken(), refund(), isWhatsapp(), advancePayment(), balanceDue(), logStatus()

### New Livewire Components
- `App\Livewire\Admin\WhatsappOrders` → route `admin.whatsapp-orders` (/admin/whatsapp-orders) — generate tokens, view token list, copy links
- `App\Livewire\Webpage\WhatsappOrderForm` → route `whatsapp.order.form` (/order/whatsapp/{token}) — PUBLIC, no auth, no maintenance gate

### Settings Added
- `advance_payment_percentage` — integer 1-100, default 50 — controls advance % for all order types
- `bank_transfer_details` — text — bank name/account shown to customers for payment

### Updated Admin Components
- `App\Livewire\Admin\Order` — expanded with: confirmPayment(), rejectPayment(), confirmOrder(), markSourcing(), markSupplierReceived(), markSupplierUnavailable(), markDispatched(), markDelivered(), markCompleted(), offerRefund(), offerReorder(), openRefundModal(), processRefund(), sendBalanceReminder(), closeDetail()
- Order view: source badges (Web/WA), receipt status indicators, contextual quick action buttons per status, full slide-over detail panel with status timeline, payment review, supplier section, refund modal

### Admin Sidebar
- "WhatsApp Orders" nav item added under Orders with WhatsApp green icon, badge shows pending token count
- Orders badge changed from 'pending' status to 'new' status count

### Public Route (No Auth)
- `/order/whatsapp/{token}` declared BEFORE `website.live` middleware group so it works during maintenance mode

### Status Badge Mapping
- new=slate, payment_received=amber, confirmed=blue, sourcing=orange, dispatched=indigo, delivered=teal, completed=green, refunded=red, cancelled=red
- Supplier: none=slate, ordered=orange, received=green, unavailable=red

## Business Logic
- Tax: 15% of subtotal
- Free shipping: orders >= Rs. 500 (otherwise Rs. 50 flat fee)
- Cart: logged-in users use DB (carts table), guests use session
- Order number format: `MH-` + uppercase uniqid()
- Product slug: Str::slug(name), guaranteed unique
- Manual orders: stock NOT deducted on creation — deducted only on bulk confirmation in Purchasing page
- Supplier purchasing: stock is ADDED to products only when goods are received via `receiveGoods()` in Purchasing Livewire component — quantity_received increments per item, product.stock increments accordingly; PO status moves draft→ordered→partial|received
- PO number format: `PO-` + 6 random uppercase chars + `-YYYYMMDD` (e.g. PO-ABCDEF-20260405)
- Supplier deletion blocked if any purchase orders exist — deactivate instead
- Order model: items relation is `items()` (HasMany OrderItem) — NOT `orderItems()`

## Design System
- **CSS file**: `resources/css/app.css` — Tailwind v4 with custom components
- **Custom classes**: `.btn-primary`, `.btn-secondary`, `.btn-sm/.btn-lg`, `.card`, `.stat-card`, `.badge-*`, `.form-input`, `.data-table`, `.product-card`, `.sidebar-nav-item`, `.container-page`
- **Color palette**: Navy #0F172A, Gold #F59E0B, White #FFFFFF, Light Gray #F8FAFC

## Currency & Location
- Currency: Rs. — format: "Rs. X,XXX" (updated April 2026 from ETB)
- SettingSeeder: currency = 'Rs.', currency_symbol = 'Rs.'
- Location: Bole Road, Addis Ababa, Ethiopia
- Tagline: "Quality You Can Trust"
- Support: support@meharahouse.com / +251 911 000 000

**Why:** Full-stack backend built in April 2026.
**How to apply:** Follow these naming, schema, and business logic conventions for all additions.
