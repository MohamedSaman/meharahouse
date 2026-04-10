<?php

// app/Livewire/Admin/WebsiteSettings.php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Setting;

#[Title('Website Settings')]
#[Layout('layouts.admin')]
class WebsiteSettings extends Component
{
    // ── Live Status ──────────────────────────────────────────────
    public bool $websiteLive        = true;
    public string $maintenanceMessage = '';
    public string $maintenanceTitle   = '';

    // ── Announcement Bar ─────────────────────────────────────────
    public bool $announcementEnabled = true;
    public string $announcementText  = '';

    // ── Site Info ────────────────────────────────────────────────
    public string $siteName     = '';
    public string $siteTagline  = '';
    public string $siteEmail    = '';
    public string $sitePhone    = '';
    public string $siteWhatsapp = '';
    public string $siteAddress  = '';

    // ── Social Links ─────────────────────────────────────────────
    public string $socialFacebook  = '';
    public string $socialInstagram = '';
    public string $socialTikTok    = '';
    public string $socialYoutube   = '';

    // ── Order Settings ───────────────────────────────────────────
    public int    $advancePaymentPercentage = 50;
    public float  $taxRate                  = 15;
    public string $bankTransferDetails      = '';

    // ── Delivery Fee ─────────────────────────────────────────────
    public bool  $deliveryFeeEnabled = false;
    public float $deliveryFeeAmount  = 0;

    public function mount(): void
    {
        // Live Status
        $this->websiteLive        = (bool) (Setting::get('website_live', '1') === '1' || Setting::get('website_live', '1') === true);
        $this->maintenanceMessage = Setting::get('maintenance_message', 'We are performing maintenance. Back shortly!') ?? '';
        $this->maintenanceTitle   = Setting::get('maintenance_title', 'Site Under Maintenance') ?? '';

        // Announcement Bar
        $this->announcementEnabled = (bool) (Setting::get('announcement_enabled', '1') === '1' || Setting::get('announcement_enabled', '1') === true);
        $this->announcementText    = Setting::get('announcement_text', 'Free Delivery on Orders Over Rs. 500 | New Arrivals Every Week | Modest Fashion for Every Woman') ?? '';

        // Site Info
        $this->siteName     = Setting::get('site_name', 'Meharahouse') ?? '';
        $this->siteTagline  = Setting::get('site_tagline', 'Elegance in Every Thread') ?? '';
        $this->siteEmail    = Setting::get('site_email', '') ?? '';
        $this->sitePhone    = Setting::get('site_phone', '') ?? '';
        $this->siteWhatsapp = Setting::get('site_whatsapp', '') ?? '';
        $this->siteAddress  = Setting::get('site_address', '') ?? '';

        // Social Links
        $this->socialFacebook  = Setting::get('social_facebook', '') ?? '';
        $this->socialInstagram = Setting::get('social_instagram', '') ?? '';
        $this->socialTikTok    = Setting::get('social_tiktok', '') ?? '';
        $this->socialYoutube   = Setting::get('social_youtube', '') ?? '';

        // Order Settings
        $this->advancePaymentPercentage = (int) Setting::get('advance_payment_percentage', '50');
        $this->taxRate                  = (float) Setting::get('tax_rate', '15');
        $this->bankTransferDetails      = Setting::get('bank_transfer_details', '') ?? '';

        // Delivery Fee
        $this->deliveryFeeEnabled = Setting::get('delivery_fee_enabled', '0') === '1';
        $this->deliveryFeeAmount  = (float) Setting::get('delivery_fee_amount', '0');
    }

    // ── Save: Live Status ─────────────────────────────────────────
    public function saveLiveStatus(): void
    {
        $this->validate([
            'maintenanceTitle'   => ['nullable', 'string', 'max:255'],
            'maintenanceMessage' => ['nullable', 'string', 'max:1000'],
        ]);

        Setting::set('website_live', $this->websiteLive ? '1' : '0');
        Setting::set('maintenance_title', $this->maintenanceTitle);
        Setting::set('maintenance_message', $this->maintenanceMessage);

        session()->flash('success_live', $this->websiteLive
            ? 'Website is now LIVE and visible to all visitors.'
            : 'Maintenance mode enabled. Visitors will see the maintenance page.');
    }

    // ── Save: Announcement Bar ────────────────────────────────────
    public function saveAnnouncement(): void
    {
        $this->validate([
            'announcementText' => ['nullable', 'string', 'max:500'],
        ]);

        Setting::set('announcement_enabled', $this->announcementEnabled ? '1' : '0');
        Setting::set('announcement_text', $this->announcementText);

        session()->flash('success_announcement', 'Announcement bar settings saved.');
    }

    // ── Save: Site Info ────────────────────────────────────────────
    public function saveSiteInfo(): void
    {
        $this->validate([
            'siteName'     => ['required', 'string', 'max:100'],
            'siteTagline'  => ['nullable', 'string', 'max:200'],
            'siteEmail'    => ['nullable', 'email', 'max:255'],
            'sitePhone'    => ['nullable', 'string', 'max:30'],
            'siteWhatsapp' => ['nullable', 'string', 'max:30'],
            'siteAddress'  => ['nullable', 'string', 'max:500'],
        ]);

        Setting::set('site_name', $this->siteName);
        Setting::set('site_tagline', $this->siteTagline);
        Setting::set('site_email', $this->siteEmail);
        Setting::set('site_phone', $this->sitePhone);
        Setting::set('site_whatsapp', $this->siteWhatsapp);
        Setting::set('site_address', $this->siteAddress);

        session()->flash('success_siteinfo', 'Site information saved successfully.');
    }

    // ── Save: Social Links ─────────────────────────────────────────
    public function saveSocialLinks(): void
    {
        $this->validate([
            'socialFacebook'  => ['nullable', 'url', 'max:255'],
            'socialInstagram' => ['nullable', 'url', 'max:255'],
            'socialTikTok'    => ['nullable', 'url', 'max:255'],
            'socialYoutube'   => ['nullable', 'url', 'max:255'],
        ]);

        Setting::set('social_facebook', $this->socialFacebook);
        Setting::set('social_instagram', $this->socialInstagram);
        Setting::set('social_tiktok', $this->socialTikTok);
        Setting::set('social_youtube', $this->socialYoutube);

        session()->flash('success_social', 'Social media links saved successfully.');
    }

    // ── Save: Order Settings ──────────────────────────────────────
    public function saveOrderSettings(): void
    {
        $this->validate([
            'advancePaymentPercentage' => ['required', 'integer', 'min:1', 'max:100'],
            'taxRate'                  => ['required', 'numeric', 'min:0', 'max:100'],
            'bankTransferDetails'      => ['nullable', 'string', 'max:1000'],
            'deliveryFeeAmount'        => ['required_if:deliveryFeeEnabled,true', 'numeric', 'min:0'],
        ]);

        Setting::set('advance_payment_percentage', (string) $this->advancePaymentPercentage);
        Setting::set('tax_rate', (string) $this->taxRate);
        Setting::set('bank_transfer_details', $this->bankTransferDetails);
        Setting::set('delivery_fee_enabled', $this->deliveryFeeEnabled ? '1' : '0');
        Setting::set('delivery_fee_amount', (string) $this->deliveryFeeAmount);

        session()->flash('success_order_settings', 'Order settings saved successfully.');
    }

    public function render()
    {
        return view('livewire.admin.website-settings');
    }
}
