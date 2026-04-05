---
name: Meharahouse Project Architecture
description: Core tech stack, module structure, route naming, and file organization for the Meharahouse e-commerce platform
type: project
---

## Tech Stack
- **Framework**: Laravel 13 (PHP 8.3+)
- **Frontend**: Tailwind CSS v4 (via @tailwindcss/vite), Alpine.js v3 (CDN), Livewire v4
- **Fonts**: Google Fonts — Inter (body), Poppins (headings) — loaded in each layout
- **Build tool**: Vite 8 with laravel-vite-plugin

## Three-Module Architecture

### Webpage (Public Storefront)
- Routes prefix: `/` with name prefix `webpage.`
- Livewire components: `app/Livewire/Webpage/`
- Views: `resources/views/livewire/webpage/`
- Layout: `resources/views/layouts/webpage.blade.php`
- Color theme: Navy (#0F172A) + Gold (#F59E0B) on white

### Admin Panel
- Routes prefix: `admin/` with name prefix `admin.`
- Livewire components: `app/Livewire/Admin/`
- Views: `resources/views/livewire/admin/`
- Layout: `resources/views/layouts/admin.blade.php`
- Color theme: Dark navy sidebar (#0F172A), gold active state

### Staff Panel
- Routes prefix: `staff/` with name prefix `staff.`
- Livewire components: `app/Livewire/Staff/`
- Views: `resources/views/livewire/staff/`
- Layout: `resources/views/layouts/staff.blade.php`
- Color theme: Deep teal sidebar (#134e4a), gold accents

## Route Naming Convention
- `webpage.home`, `webpage.shop`, `webpage.product-details`, `webpage.cart`, `webpage.checkout`, `webpage.orders`, `webpage.about`, `webpage.contact`
- `admin.dashboard`, `admin.orders`, `admin.products`, `admin.categories`, `admin.customers`, `admin.payments`, `admin.reports`
- `staff.dashboard`, `staff.orders`, `staff.customers`

## Design System
- **CSS file**: `resources/css/app.css` — contains Tailwind v4 `@theme` block, `@layer base`, `@layer components`, `@layer utilities`
- **Custom classes**: `.btn-primary`, `.btn-secondary`, `.btn-ghost`, `.btn-danger`, `.btn-sm`, `.btn-lg`, `.card`, `.card-hover`, `.stat-card`, `.badge`, `.badge-success/warning/danger/info/navy/gold`, `.form-input`, `.form-label`, `.data-table`, `.section-title`, `.section-subtitle`, `.section-label`, `.gold-divider`, `.product-card`, `.sidebar-nav-item`, `.container-page`
- **Color vars**: `--color-navy-*` and `--color-gold-*` (50–950)

## Livewire Layout Pattern
Components use `->layout('layouts.admin')` and `->layoutData([...])` in the `render()` method. Linter may add PHP 8 `#[Layout]` and `#[Title]` attributes — both patterns are valid for Laravel 13 + Livewire v4.

## Currency
Ethiopian Birr (ETB) — formatted as "ETB X,XXX"

## Key Business Context
- Located in Addis Ababa, Ethiopia (Bole Road)
- Tagline: "Quality You Can Trust"
- Support: support@meharahouse.com / +251 911 000 000
- Payment methods displayed: CBE Birr, Telebirr, Cash, Visa, MC, PayPal

**Why:** Foundational decisions made during the initial UI build session (April 2026).
**How to apply:** All new pages, components, and routes should follow these naming conventions, design tokens, and module boundaries.
