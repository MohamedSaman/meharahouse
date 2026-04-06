<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Setting;

#[Title('WhatsApp Integration')]
#[Layout('layouts.admin')]
class WhatsappIntegration extends Component
{
    // ── Twilio ────────────────────────────────────────────────────────────
    public bool   $twilio_enabled     = false;
    public string $twilio_account_sid = '';
    public string $twilio_auth_token  = '';
    public string $twilio_from_number = '';

    // ── 360dialog ─────────────────────────────────────────────────────────
    public bool   $dialog360_enabled      = false;
    public string $dialog360_api_key      = '';
    public string $dialog360_phone_number = '';

    // ── Meta Cloud API ────────────────────────────────────────────────────
    public bool   $meta_enabled              = false;
    public string $meta_phone_number_id      = '';
    public string $meta_access_token         = '';
    public string $meta_business_account_id  = '';

    // ── UI state ──────────────────────────────────────────────────────────
    public string $activeProvider = ''; // which provider card is expanded
    public string $savedProvider  = ''; // for success feedback (matches pattern of $savedGateway)

    public function mount(): void
    {
        $this->twilio_enabled     = Setting::get('whatsapp_twilio_enabled', '0') === '1';
        $this->twilio_account_sid = Setting::get('whatsapp_twilio_account_sid', '');
        $this->twilio_auth_token  = Setting::get('whatsapp_twilio_auth_token', '');
        $this->twilio_from_number = Setting::get('whatsapp_twilio_from_number', '');

        $this->dialog360_enabled      = Setting::get('whatsapp_dialog360_enabled', '0') === '1';
        $this->dialog360_api_key      = Setting::get('whatsapp_dialog360_api_key', '');
        $this->dialog360_phone_number = Setting::get('whatsapp_dialog360_phone_number', '');

        $this->meta_enabled             = Setting::get('whatsapp_meta_enabled', '0') === '1';
        $this->meta_phone_number_id     = Setting::get('whatsapp_meta_phone_number_id', '');
        $this->meta_access_token        = Setting::get('whatsapp_meta_access_token', '');
        $this->meta_business_account_id = Setting::get('whatsapp_meta_business_account_id', '');
    }

    public function toggleProvider(string $provider): void
    {
        $this->activeProvider = $this->activeProvider === $provider ? '' : $provider;
    }

    /**
     * When a provider is enabled, disable the other two (only one active at a time).
     */
    private function enforceExclusivity(string $active): void
    {
        if ($active !== 'twilio')   $this->twilio_enabled   = false;
        if ($active !== 'dialog360') $this->dialog360_enabled = false;
        if ($active !== 'meta')     $this->meta_enabled     = false;
    }

    public function saveTwilio(): void
    {
        $this->validate([
            'twilio_account_sid' => $this->twilio_enabled ? 'required|string' : 'nullable',
            'twilio_auth_token'  => $this->twilio_enabled ? 'required|string' : 'nullable',
            'twilio_from_number' => $this->twilio_enabled ? 'required|string' : 'nullable',
        ]);

        if ($this->twilio_enabled) {
            $this->enforceExclusivity('twilio');
        }

        Setting::set('whatsapp_provider',           $this->twilio_enabled ? 'twilio' : '');
        Setting::set('whatsapp_twilio_enabled',     $this->twilio_enabled ? '1' : '0');
        Setting::set('whatsapp_twilio_account_sid', $this->twilio_account_sid);
        Setting::set('whatsapp_twilio_auth_token',  $this->twilio_auth_token);
        Setting::set('whatsapp_twilio_from_number', $this->twilio_from_number);

        // Persist the disable state for the other two providers
        if ($this->twilio_enabled) {
            Setting::set('whatsapp_dialog360_enabled', '0');
            Setting::set('whatsapp_meta_enabled',      '0');
        }

        $this->savedProvider = 'twilio';
    }

    public function saveDialog360(): void
    {
        $this->validate([
            'dialog360_api_key'      => $this->dialog360_enabled ? 'required|string' : 'nullable',
            'dialog360_phone_number' => $this->dialog360_enabled ? 'required|string' : 'nullable',
        ]);

        if ($this->dialog360_enabled) {
            $this->enforceExclusivity('dialog360');
        }

        Setting::set('whatsapp_provider',                $this->dialog360_enabled ? '360dialog' : '');
        Setting::set('whatsapp_dialog360_enabled',       $this->dialog360_enabled ? '1' : '0');
        Setting::set('whatsapp_dialog360_api_key',       $this->dialog360_api_key);
        Setting::set('whatsapp_dialog360_phone_number',  $this->dialog360_phone_number);

        if ($this->dialog360_enabled) {
            Setting::set('whatsapp_twilio_enabled', '0');
            Setting::set('whatsapp_meta_enabled',   '0');
        }

        $this->savedProvider = 'dialog360';
    }

    public function saveMeta(): void
    {
        $this->validate([
            'meta_phone_number_id'     => $this->meta_enabled ? 'required|string' : 'nullable',
            'meta_access_token'        => $this->meta_enabled ? 'required|string' : 'nullable',
            'meta_business_account_id' => $this->meta_enabled ? 'required|string' : 'nullable',
        ]);

        if ($this->meta_enabled) {
            $this->enforceExclusivity('meta');
        }

        Setting::set('whatsapp_provider',                  $this->meta_enabled ? 'meta' : '');
        Setting::set('whatsapp_meta_enabled',              $this->meta_enabled ? '1' : '0');
        Setting::set('whatsapp_meta_phone_number_id',      $this->meta_phone_number_id);
        Setting::set('whatsapp_meta_access_token',         $this->meta_access_token);
        Setting::set('whatsapp_meta_business_account_id',  $this->meta_business_account_id);

        if ($this->meta_enabled) {
            Setting::set('whatsapp_twilio_enabled',   '0');
            Setting::set('whatsapp_dialog360_enabled', '0');
        }

        $this->savedProvider = 'meta';
    }

    public function render()
    {
        return view('livewire.admin.whatsapp-integration');
    }
}
