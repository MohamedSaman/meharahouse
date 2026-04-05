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
- Color theme: Navy (#0F172A) + Gold (#F59E0B) on white

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
- Webpage: `webpage.home`, `webpage.shop`, `webpage.product-details`, `webpage.cart`, `webpage.checkout`, `webpage.orders`, `webpage.about`, `webpage.contact`
- Admin: `admin.dashboard`, `admin.orders`, `admin.products`, `admin.categories`, `admin.customers`, `admin.payments`, `admin.reports`
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

## Seeded Demo Data
- Admin: admin@meharahouse.com / password
- Staff: staff@meharahouse.com / password
- Customers: abebe@example.com, tigist@example.com, solomon@example.com, marta@example.com, dawit@example.com (all password: password)
- 10 categories, 30 products, 4 banners, 4 coupons (WELCOME10, SAVE50, MEHAR20, FREESHIP), 16 settings, 20 sample orders

## Business Logic
- Tax: 15% of subtotal
- Free shipping: orders >= ETB 500 (otherwise ETB 50 flat fee)
- Cart: logged-in users use DB (carts table), guests use session
- Order number format: `MH-` + uppercase uniqid()
- Product slug: Str::slug(name), guaranteed unique

## Design System
- **CSS file**: `resources/css/app.css` — Tailwind v4 with custom components
- **Custom classes**: `.btn-primary`, `.btn-secondary`, `.btn-sm/.btn-lg`, `.card`, `.stat-card`, `.badge-*`, `.form-input`, `.data-table`, `.product-card`, `.sidebar-nav-item`, `.container-page`
- **Color palette**: Navy #0F172A, Gold #F59E0B, White #FFFFFF, Light Gray #F8FAFC

## Currency & Location
- Currency: ETB (Ethiopian Birr) — format: "ETB X,XXX"
- Location: Bole Road, Addis Ababa, Ethiopia
- Tagline: "Quality You Can Trust"
- Support: support@meharahouse.com / +251 911 000 000

**Why:** Full-stack backend built in April 2026.
**How to apply:** Follow these naming, schema, and business logic conventions for all additions.
