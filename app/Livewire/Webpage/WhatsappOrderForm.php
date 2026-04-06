<?php

namespace App\Livewire\Webpage;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use App\Models\WhatsappOrderToken;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Setting;

#[Title('Complete Your Order')]
#[Layout('layouts.minimal')]
class WhatsappOrderForm extends Component
{
    use WithFileUploads;

    // ── Route Parameter ───────────────────────────────────────────────
    public string $token = '';

    // ── Token State ───────────────────────────────────────────────────
    public ?WhatsappOrderToken $tokenModel = null;
    public bool $tokenInvalid = false;

    // ── Customer Details ──────────────────────────────────────────────
    public string $customerName  = '';
    public string $customerPhone = '';
    public string $customerEmail = '';

    // ── Delivery Address ──────────────────────────────────────────────
    public string $addressLine = '';
    public string $city        = '';
    public string $district    = '';
    public string $region      = 'N/A';
    public string $notes       = '';

    // ── Alternate Contact ─────────────────────────────────────────────
    public string $altPhone    = '';

    // ── Abaya Details ─────────────────────────────────────────────────
    public string $abayas      = ''; // size
    public string $abayaModel  = '';

    // ── Payment ───────────────────────────────────────────────────────
    public $receiptFile = null;

    // ── Submission State ──────────────────────────────────────────────
    public bool    $submitted        = false;
    public string  $createdOrderNumber = '';

    public function mount(string $token): void
    {
        $this->token = $token;

        // Load and validate the token
        $this->tokenModel = WhatsappOrderToken::where('token', $token)->first();

        if (!$this->tokenModel || !$this->tokenModel->isUsable()) {
            $this->tokenInvalid = true;
        }
    }

    public function submit(): void
    {
        // Double-check token validity on submit (race condition protection)
        if ($this->tokenInvalid || !$this->tokenModel || !$this->tokenModel->isUsable()) {
            $this->tokenInvalid = true;
            return;
        }

        $this->validate([
            'customerName'  => ['required', 'string', 'max:150'],
            'customerPhone' => ['required', 'string', 'max:30'],
            'altPhone'      => ['nullable', 'string', 'max:30'],
            'customerEmail' => ['nullable', 'email', 'max:255'],
            'addressLine'   => ['required', 'string', 'max:500'],
            'city'          => ['required', 'string', 'max:100'],
            'district'      => ['nullable', 'string', 'max:100'],
            'region'        => ['required', 'string', 'max:100'],
            'abayas'        => ['nullable', 'string', 'max:100'],
            'abayaModel'    => ['nullable', 'string', 'max:200'],
            'notes'         => ['nullable', 'string', 'max:1000'],
            'receiptFile'   => ['required', 'image', 'max:30720'], // 30MB — iPhone Pro Max photos
        ]);

        // Store the uploaded receipt
        $receiptPath = $this->receiptFile->store('payment-receipts', 'public');

        // Create the Order record
        $order = Order::create([
            'user_id'            => null, // Guest order — no account required
            'order_number'       => Order::generateOrderNumber(),
            'source'             => 'whatsapp',
            'status'             => 'new',
            'advance_percentage' => $this->tokenModel->advance_percentage,
            'advance_amount'     => $this->tokenModel->advance_amount,
            'balance_amount'     => (float) $this->tokenModel->subtotal - (float) $this->tokenModel->advance_amount,
            'subtotal'           => $this->tokenModel->subtotal,
            'tax'                => 0,
            'shipping_cost'      => 0,
            'discount'           => 0,
            'total'              => $this->tokenModel->subtotal,
            'payment_method'     => 'bank_transfer',
            'payment_status'     => 'pending',
            'shipping_address'   => [
                'full_name'   => $this->customerName,
                'phone'       => $this->customerPhone,
                'alt_phone'   => $this->altPhone ?: null,
                'email'       => $this->customerEmail ?: null,
                'address'     => $this->addressLine,
                'city'        => $this->city,
                'district'    => $this->district ?: null,
                'region'      => $this->region,
                'abaya_size'  => $this->abayas ?: null,
                'abaya_model' => $this->abayaModel ?: null,
            ],
            'notes' => $this->notes ?: null,
        ]);

        // Create OrderItem records from the token's product list
        foreach ($this->tokenModel->products as $product) {
            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $product['product_id'],
                'product_name' => $product['product_name'],
                'price'        => $product['price'],
                'quantity'     => $product['quantity'],
                'subtotal'     => $product['price'] * $product['quantity'],
            ]);
        }

        // Record the advance payment receipt (pending confirmation by admin)
        OrderPayment::create([
            'order_id'     => $order->id,
            'type'         => 'advance',
            'amount'       => $this->tokenModel->advance_amount,
            'method'       => 'bank_transfer',
            'receipt_path' => $receiptPath,
            'status'       => 'pending',
        ]);

        // Create the initial status log entry
        $order->statusLogs()->create([
            'from_status' => null,
            'to_status'   => 'new',
            'notes'       => 'Order placed via WhatsApp link by customer.',
            'created_by'  => null,
        ]);

        // Mark the token as used — prevents reuse
        $this->tokenModel->markUsed($order->id);

        // Success state
        $this->submitted          = true;
        $this->createdOrderNumber = $order->order_number;
    }

    public function render()
    {
        $bankDetails = Setting::get('bank_transfer_details', '');

        return view('livewire.webpage.whatsapp-order-form', [
            'bankDetails' => $bankDetails,
        ]);
    }
}
