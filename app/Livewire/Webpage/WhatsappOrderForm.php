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
use App\Models\Product;
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

    // ── Per-product variant selections (keyed by product index) ──────
    public array $productSizes   = []; // string chip selection per product
    public array $productColors  = []; // string chip selection per product
    public array $productVariants = []; // [{sizes:[], colors:[]}] indexed by product index

    // ── Payment ───────────────────────────────────────────────────────
    public string $paymentOption = 'advance'; // 'advance' or 'full' — BUG WA-1.1 fix
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
        } else {
            $products = $this->tokenModel->products ?? [];
            $count    = count($products);

            $this->productSizes  = array_fill(0, $count, '');
            $this->productColors = array_fill(0, $count, '');

            // Load sizes/colors for each product from DB
            $productIds = collect($products)->pluck('product_id')->filter()->unique()->toArray();
            $dbProducts = Product::whereIn('id', $productIds)
                ->get(['id', 'sizes', 'colors'])
                ->keyBy('id');

            foreach ($products as $i => $p) {
                $db = $dbProducts->get($p['product_id']);
                $this->productVariants[$i] = [
                    'sizes'  => $db?->sizes  ?? [],
                    'colors' => $db?->colors ?? [],
                ];
            }
        }
    }

    public function selectSize(int $index, string $size): void
    {
        $this->productSizes[$index] = $size;
    }

    public function selectColor(int $index, string $color): void
    {
        $this->productColors[$index] = $color;
    }

    public function submit(): void
    {
        // Double-check token validity on submit (race condition protection)
        if ($this->tokenInvalid || !$this->tokenModel || !$this->tokenModel->isUsable()) {
            $this->tokenInvalid = true;
            return;
        }

        // Build per-product size/color validation rules
        $variantRules    = [];
        $variantMessages = [];
        foreach ($this->tokenModel->products as $i => $p) {
            $v = $this->productVariants[$i] ?? [];
            if (!empty($v['sizes'])) {
                $variantRules["productSizes.{$i}"]    = ['required', 'string', 'in:' . implode(',', $v['sizes'])];
                $variantMessages["productSizes.{$i}.required"] = "Please select a size for \"{$p['product_name']}\".";
                $variantMessages["productSizes.{$i}.in"]       = "Please select a valid size for \"{$p['product_name']}\".";
            }
            if (!empty($v['colors'])) {
                $colorNames = collect($v['colors'])->pluck('name')->implode(',');
                $variantRules["productColors.{$i}"]    = ['required', 'string', 'in:' . $colorNames];
                $variantMessages["productColors.{$i}.required"] = "Please select a color for \"{$p['product_name']}\".";
                $variantMessages["productColors.{$i}.in"]       = "Please select a valid color for \"{$p['product_name']}\".";
            }
        }

        $this->validate(array_merge([
            'customerName'  => ['required', 'string', 'max:150'],
            'customerPhone' => ['required', 'string', 'max:30'],
            'altPhone'      => ['nullable', 'string', 'max:30'],
            'customerEmail' => ['nullable', 'email', 'max:255'],
            'addressLine'   => ['required', 'string', 'max:500'],
            'city'          => ['required', 'string', 'max:100'],
            'district'      => ['nullable', 'string', 'max:100'],
            'region'        => ['required', 'string', 'max:100'],
            'notes'         => ['nullable', 'string', 'max:1000'],
            'receiptFile'   => ['required', 'image', 'max:30720'],
            'paymentOption' => ['required', 'in:advance,full'],
        ], $variantRules), $variantMessages);

        // Store the uploaded receipt
        $receiptPath = $this->receiptFile->store('payment-receipts', 'public');

        // BUG WA-1.1 fix: Calculate payment amounts based on customer's choice
        $subtotal = (float) $this->tokenModel->subtotal;
        if ($this->paymentOption === 'full') {
            $advancePct = 100;
            $advanceAmt = $subtotal;
            $balanceAmt = 0;
        } else {
            $advancePct = (float) $this->tokenModel->advance_percentage;
            $advanceAmt = (float) $this->tokenModel->advance_amount;
            $balanceAmt = $subtotal - $advanceAmt;
        }

        // Create the Order record
        $order = Order::create([
            'user_id'            => null, // Guest order — no account required
            'order_number'       => Order::generateOrderNumber(),
            'source'             => 'whatsapp',
            'status'             => 'new',
            'advance_percentage' => $advancePct,
            'advance_amount'     => $advanceAmt,
            'balance_amount'     => $balanceAmt,
            'subtotal'           => $subtotal,
            'tax'                => 0,
            'shipping_cost'      => 0,
            'discount'           => 0,
            'total'              => $subtotal,
            'payment_method'     => 'bank_transfer',
            'payment_status'     => 'pending',
            'shipping_address'   => [
                'full_name' => $this->customerName,
                'phone'     => $this->customerPhone,
                'alt_phone' => $this->altPhone ?: null,
                'email'     => $this->customerEmail ?: null,
                'address'   => $this->addressLine,
                'city'      => $this->city,
                'district'  => $this->district ?: null,
                'region'    => $this->region,
            ],
            'notes' => $this->notes ?: null,
        ]);

        // Create OrderItem records from the token's product list
        foreach ($this->tokenModel->products as $i => $product) {
            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $product['product_id'],
                'product_name' => $product['product_name'],
                'price'        => $product['price'],
                'quantity'     => $product['quantity'],
                'subtotal'     => $product['price'] * $product['quantity'],
                'size'         => $this->productSizes[$i]  ?: null,
                'color'        => $this->productColors[$i] ?: null,
            ]);
        }

        // Record the payment receipt (pending confirmation by admin)
        OrderPayment::create([
            'order_id'     => $order->id,
            'type'         => $this->paymentOption === 'full' ? 'full' : 'advance',
            'amount'       => $advanceAmt,
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
