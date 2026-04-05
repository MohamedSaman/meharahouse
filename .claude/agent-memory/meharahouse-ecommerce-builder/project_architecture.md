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
- Admin: `admin.dashboard`, `admin.orders`, `admin.products`, `admin.categories`, `admin.customers`, `admin.payments`, `admin.reports`, `admin.manual-order`, `admin.suppliers`, `admin.purchasing`
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
