---
name: Jetstream Installation State
description: Jetstream v5.5 + Fortify v1.36 installation details, Livewire downgrade, 2FA config, and profile page setup
type: project
---

Laravel Jetstream v5.5.2 and Fortify v1.36.2 are installed. Livewire was downgraded from v4.2.4 to v3.7.15 as a Jetstream v5 requirement — existing components use PHP 8.0 `#[...]` attributes which are compatible with both versions.

**Packages added:** laravel/jetstream ^5.5, laravel/fortify v1.36, laravel/sanctum v4.3 (also added), livewire/livewire downgraded to ^3.6.

**Key decisions:**
- `Features::twoFactorAuthentication()` does NOT exist on `Laravel\Jetstream\Features` in v5 — 2FA is controlled entirely via `config/fortify.php` using `Laravel\Fortify\Features::twoFactorAuthentication()`.
- Jetstream `features` array in `config/jetstream.php` only has `profilePhotos()` and `accountDeletion()`.
- Role-based post-login redirect uses custom `LoginResponse` and `TwoFactorLoginResponse` bindings in `FortifyServiceProvider::register()` — NOT `Fortify::redirectUsersTo()` (that method does not exist in Fortify v1).
- Fortify login/register views redirect to existing custom Livewire routes (`auth.login`, `auth.register`).
- Profile page: `App\Livewire\Admin\Profile` serves both admin (`/admin/profile`) and staff (`/staff/profile`), auto-detecting the correct layout via `auth()->user()->isAdmin()`.

**Database:** `profile_photo_path` column added via migration `2026_04_06_082548_add_profile_photo_path_to_users_table`. Two-factor columns (`two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`) already existed.

**Why:** Full Jetstream install adds profile photos, 2FA, browser session management, and account deletion for admin/staff users.

**How to apply:** When adding new auth-related features, check Fortify config (not Jetstream config) for 2FA. Always use `LoginResponse` contract binding for redirect logic — not a static Fortify method.
