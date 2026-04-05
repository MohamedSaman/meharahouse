<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Setting;

#[Title('Payment Integration')]
#[Layout('layouts.admin')]
class PaymentIntegration extends Component
{
    // ── Cash on Delivery ──────────────────────────────────────────────
    public bool $cod_enabled = false;

    // ── PayHere ───────────────────────────────────────────────────────
    public bool   $payhere_enabled     = false;
    public string $payhere_merchant_id = '';
    public string $payhere_secret      = '';
    public string $payhere_mode        = 'sandbox'; // sandbox | live

    // ── PayPal ────────────────────────────────────────────────────────
    public bool   $paypal_enabled       = false;
    public string $paypal_client_id     = '';
    public string $paypal_client_secret = '';
    public string $paypal_mode          = 'sandbox'; // sandbox | live

    // ── Stripe ────────────────────────────────────────────────────────
    public bool   $stripe_enabled          = false;
    public string $stripe_publishable_key  = '';
    public string $stripe_secret_key       = '';

    // ── TeleBirr (Ethiopia) ───────────────────────────────────────────
    public bool   $telebirr_enabled    = false;
    public string $telebirr_app_id     = '';
    public string $telebirr_app_key    = '';
    public string $telebirr_short_code = '';
    public string $telebirr_public_key = '';

    // ── CBE Birr (Ethiopia) ───────────────────────────────────────────
    public bool   $cbebirr_enabled     = false;
    public string $cbebirr_api_key     = '';
    public string $cbebirr_merchant_id = '';
    public string $cbebirr_secret      = '';

    // ── Bank Transfer ─────────────────────────────────────────────────
    public bool   $bank_enabled          = false;
    public string $bank_account_name     = '';
    public string $bank_account_number   = '';
    public string $bank_name             = '';
    public string $bank_branch           = '';
    public string $bank_instructions     = '';

    public string $activeGateway = ''; // which gateway's form is expanded
    public string $savedGateway  = ''; // for success feedback

    public function mount(): void
    {
        $this->cod_enabled = Setting::get('payment_cod_enabled', '1') === '1';

        $this->payhere_enabled     = Setting::get('payment_payhere_enabled', '0') === '1';
        $this->payhere_merchant_id = Setting::get('payment_payhere_merchant_id', '');
        $this->payhere_secret      = Setting::get('payment_payhere_secret', '');
        $this->payhere_mode        = Setting::get('payment_payhere_mode', 'sandbox');

        $this->paypal_enabled       = Setting::get('payment_paypal_enabled', '0') === '1';
        $this->paypal_client_id     = Setting::get('payment_paypal_client_id', '');
        $this->paypal_client_secret = Setting::get('payment_paypal_client_secret', '');
        $this->paypal_mode          = Setting::get('payment_paypal_mode', 'sandbox');

        $this->stripe_enabled         = Setting::get('payment_stripe_enabled', '0') === '1';
        $this->stripe_publishable_key = Setting::get('payment_stripe_publishable_key', '');
        $this->stripe_secret_key      = Setting::get('payment_stripe_secret_key', '');

        $this->telebirr_enabled    = Setting::get('payment_telebirr_enabled', '0') === '1';
        $this->telebirr_app_id     = Setting::get('payment_telebirr_app_id', '');
        $this->telebirr_app_key    = Setting::get('payment_telebirr_app_key', '');
        $this->telebirr_short_code = Setting::get('payment_telebirr_short_code', '');
        $this->telebirr_public_key = Setting::get('payment_telebirr_public_key', '');

        $this->cbebirr_enabled     = Setting::get('payment_cbebirr_enabled', '0') === '1';
        $this->cbebirr_api_key     = Setting::get('payment_cbebirr_api_key', '');
        $this->cbebirr_merchant_id = Setting::get('payment_cbebirr_merchant_id', '');
        $this->cbebirr_secret      = Setting::get('payment_cbebirr_secret', '');

        $this->bank_enabled          = Setting::get('payment_bank_enabled', '0') === '1';
        $this->bank_account_name     = Setting::get('payment_bank_account_name', '');
        $this->bank_account_number   = Setting::get('payment_bank_account_number', '');
        $this->bank_name             = Setting::get('payment_bank_name', '');
        $this->bank_branch           = Setting::get('payment_bank_branch', '');
        $this->bank_instructions     = Setting::get('payment_bank_instructions', '');
    }

    public function toggleGateway(string $gateway): void
    {
        $this->activeGateway = $this->activeGateway === $gateway ? '' : $gateway;
    }

    public function saveCod(): void
    {
        Setting::set('payment_cod_enabled', $this->cod_enabled ? '1' : '0');
        $this->savedGateway = 'cod';
    }

    public function savePayhere(): void
    {
        $this->validate([
            'payhere_merchant_id' => $this->payhere_enabled ? 'required|string' : 'nullable',
            'payhere_secret'      => $this->payhere_enabled ? 'required|string' : 'nullable',
            'payhere_mode'        => 'in:sandbox,live',
        ]);

        Setting::set('payment_payhere_enabled',     $this->payhere_enabled ? '1' : '0');
        Setting::set('payment_payhere_merchant_id', $this->payhere_merchant_id);
        Setting::set('payment_payhere_secret',      $this->payhere_secret);
        Setting::set('payment_payhere_mode',        $this->payhere_mode);
        $this->savedGateway = 'payhere';
    }

    public function savePaypal(): void
    {
        $this->validate([
            'paypal_client_id'     => $this->paypal_enabled ? 'required|string' : 'nullable',
            'paypal_client_secret' => $this->paypal_enabled ? 'required|string' : 'nullable',
            'paypal_mode'          => 'in:sandbox,live',
        ]);

        Setting::set('payment_paypal_enabled',       $this->paypal_enabled ? '1' : '0');
        Setting::set('payment_paypal_client_id',     $this->paypal_client_id);
        Setting::set('payment_paypal_client_secret', $this->paypal_client_secret);
        Setting::set('payment_paypal_mode',          $this->paypal_mode);
        $this->savedGateway = 'paypal';
    }

    public function saveStripe(): void
    {
        $this->validate([
            'stripe_publishable_key' => $this->stripe_enabled ? 'required|string|starts_with:pk_' : 'nullable',
            'stripe_secret_key'      => $this->stripe_enabled ? 'required|string|starts_with:sk_' : 'nullable',
        ]);

        Setting::set('payment_stripe_enabled',         $this->stripe_enabled ? '1' : '0');
        Setting::set('payment_stripe_publishable_key', $this->stripe_publishable_key);
        Setting::set('payment_stripe_secret_key',      $this->stripe_secret_key);
        $this->savedGateway = 'stripe';
    }

    public function saveTelebirr(): void
    {
        $this->validate([
            'telebirr_app_id'     => $this->telebirr_enabled ? 'required|string' : 'nullable',
            'telebirr_app_key'    => $this->telebirr_enabled ? 'required|string' : 'nullable',
            'telebirr_short_code' => $this->telebirr_enabled ? 'required|string' : 'nullable',
            'telebirr_public_key' => 'nullable|string',
        ]);

        Setting::set('payment_telebirr_enabled',    $this->telebirr_enabled ? '1' : '0');
        Setting::set('payment_telebirr_app_id',     $this->telebirr_app_id);
        Setting::set('payment_telebirr_app_key',    $this->telebirr_app_key);
        Setting::set('payment_telebirr_short_code', $this->telebirr_short_code);
        Setting::set('payment_telebirr_public_key', $this->telebirr_public_key);
        $this->savedGateway = 'telebirr';
    }

    public function saveCbebirr(): void
    {
        $this->validate([
            'cbebirr_api_key'     => $this->cbebirr_enabled ? 'required|string' : 'nullable',
            'cbebirr_merchant_id' => $this->cbebirr_enabled ? 'required|string' : 'nullable',
            'cbebirr_secret'      => $this->cbebirr_enabled ? 'required|string' : 'nullable',
        ]);

        Setting::set('payment_cbebirr_enabled',     $this->cbebirr_enabled ? '1' : '0');
        Setting::set('payment_cbebirr_api_key',     $this->cbebirr_api_key);
        Setting::set('payment_cbebirr_merchant_id', $this->cbebirr_merchant_id);
        Setting::set('payment_cbebirr_secret',      $this->cbebirr_secret);
        $this->savedGateway = 'cbebirr';
    }

    public function saveBank(): void
    {
        $this->validate([
            'bank_account_name'   => $this->bank_enabled ? 'required|string' : 'nullable',
            'bank_account_number' => $this->bank_enabled ? 'required|string' : 'nullable',
            'bank_name'           => $this->bank_enabled ? 'required|string' : 'nullable',
            'bank_branch'         => 'nullable|string',
            'bank_instructions'   => 'nullable|string|max:500',
        ]);

        Setting::set('payment_bank_enabled',          $this->bank_enabled ? '1' : '0');
        Setting::set('payment_bank_account_name',     $this->bank_account_name);
        Setting::set('payment_bank_account_number',   $this->bank_account_number);
        Setting::set('payment_bank_name',             $this->bank_name);
        Setting::set('payment_bank_branch',           $this->bank_branch);
        Setting::set('payment_bank_instructions',     $this->bank_instructions);
        $this->savedGateway = 'bank';
    }

    public function render()
    {
        return view('livewire.admin.payment-integration');
    }
}
