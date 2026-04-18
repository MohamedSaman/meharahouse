<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

#[Title('Manual Order')]
#[Layout('layouts.admin')]
class ManualOrder extends Component
{
    // Customer search
    public string $phoneSearch = '';
    public ?User $foundCustomer = null;
    public bool $showNewCustomerForm = false;
    public bool $customerConfirmed = false;

    // New customer form
    public string $newName = '';
    public string $newEmail = '';
    public string $newPhone = '';

    // Delivery address (right column)
    public string $deliveryName = '';
    public string $deliveryPhone = '';
    public string $deliveryAddress = '';
    public string $deliveryCity = '';
    public string $deliveryRegion = '';
    public string $notes = '';

    // Product selection
    public string $productSearch = '';
    public array $orderItems = []; // [unique_key => ['id', 'name', 'sku', 'price', 'stock', 'qty', 'size']]
    public string $paymentMethod = 'cash_on_delivery';

    // Success
    public ?string $createdOrderNumber = null;

    public function searchCustomer(): void
    {
        $this->foundCustomer = null;
        $this->showNewCustomerForm = false;
        $this->customerConfirmed = false;

        if (strlen($this->phoneSearch) < 5) return;

        $this->foundCustomer = User::where('phone', 'like', '%' . trim($this->phoneSearch) . '%')
            ->orWhere('name', 'like', '%' . trim($this->phoneSearch) . '%')
            ->first();

        if (!$this->foundCustomer) {
            $this->showNewCustomerForm = true;
            $this->newPhone = $this->phoneSearch;
        } else {
            // Pre-fill delivery from customer profile
            $this->deliveryName  = $this->foundCustomer->name;
            $this->deliveryPhone = $this->foundCustomer->phone ?? '';

            if ($this->foundCustomer->address) {
                $addr = is_array($this->foundCustomer->address) ? $this->foundCustomer->address : [];
                $this->deliveryAddress = $addr['address'] ?? '';
                $this->deliveryCity    = $addr['city'] ?? '';
                $this->deliveryRegion  = $addr['region'] ?? '';
            }

            $this->customerConfirmed = true;
        }
    }

    public function createNewCustomer(): void
    {
        $this->validate([
            'newName'  => 'required|string|max:255',
            'newPhone' => 'required|string|max:20',
            'newEmail' => 'nullable|email|unique:users,email',
        ]);

        $this->foundCustomer = User::create([
            'name'     => $this->newName,
            'phone'    => $this->newPhone,
            'email'    => $this->newEmail ?: ($this->newPhone . '@meharahouse.local'),
            'password' => Hash::make(Str::random(12)),
            'role'     => 'customer',
        ]);

        $this->deliveryName  = $this->newName;
        $this->deliveryPhone = $this->newPhone;
        $this->showNewCustomerForm = false;
        $this->customerConfirmed = true;
        session()->flash('info', 'New customer created: ' . $this->newName);
    }

    public function getProductResultsProperty()
    {
        if (strlen($this->productSearch) < 2) return collect();

        return Product::active()
            ->where(fn($q) => $q->where('name', 'like', "%{$this->productSearch}%")
                ->orWhere('sku', 'like', "%{$this->productSearch}%"))
            ->limit(8)
            ->get();
    }

    public function addProduct(int $productId): void
    {
        $product = Product::find($productId);
        if (!$product) return;

        $key = $productId . '_'; // default size empty string

        if (isset($this->orderItems[$key])) {
            $this->orderItems[$key]['qty']++;
        } else {
            $this->orderItems[$key] = [
                'id'    => $product->id,
                'name'  => $product->name,
                'sku'   => $product->sku,
                'price' => (float) $product->effectivePrice(),
                'stock' => $product->stock,
                'qty'   => 1,
                'size'  => '',
            ];
        }

        $this->productSearch = '';
    }

    public function updateSize(string $key, string $size): void
    {
        if (isset($this->orderItems[$key])) {
            $this->orderItems[$key]['size'] = $size;
        }
    }

    public function updateQty(string $key, int $qty): void
    {
        if ($qty < 1) {
            unset($this->orderItems[$key]);
            return;
        }

        if (isset($this->orderItems[$key])) {
            $this->orderItems[$key]['qty'] = $qty;
        }
    }

    public function removeItem(string $key): void
    {
        unset($this->orderItems[$key]);
    }

    public function getSubtotal(): float
    {
        return collect($this->orderItems)->sum(fn($i) => $i['price'] * $i['qty']);
    }

    public function placeOrder(): void
    {
        $this->validate([
            'deliveryName'    => 'required|string',
            'deliveryPhone'   => 'required|string',
            'deliveryAddress' => 'required|string',
            'deliveryCity'    => 'required|string',
        ]);

        if (!$this->foundCustomer) {
            session()->flash('error', 'Please select or create a customer first.');
            return;
        }

        if (empty($this->orderItems)) {
            session()->flash('error', 'Please add at least one product.');
            return;
        }

        $subtotal    = $this->getSubtotal();
        $total       = $subtotal;
        $orderNumber = Order::generateOrderNumber();

        DB::transaction(function () use ($subtotal, $total, $orderNumber) {
            $order = Order::create([
                'user_id'          => $this->foundCustomer->id,
                'order_number'     => $orderNumber,
                'status'           => 'new',
                'subtotal'         => $subtotal,
                'tax'              => 0,
                'shipping_cost'    => 0,
                'discount'         => 0,
                'total'            => $total,
                'shipping_address' => [
                    'full_name' => $this->deliveryName,
                    'phone'     => $this->deliveryPhone,
                    'address'   => $this->deliveryAddress,
                    'city'      => $this->deliveryCity,
                    'region'    => $this->deliveryRegion,
                ],
                'payment_method'  => $this->paymentMethod,
                'payment_status'  => 'pending',
                'notes'           => ($this->notes ? $this->notes . ' ' : '') . '[Manual Order by Admin]',
            ]);

            foreach ($this->orderItems as $key => $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['id'],
                    'product_name' => $item['name'],
                    'price'        => $item['price'],
                    'quantity'     => $item['qty'],
                    'size'         => $item['size'] ?: null,
                    'subtotal'     => $item['price'] * $item['qty'],
                ]);
                // NOTE: stock is NOT decremented here — deducted only on bulk confirmation in Purchasing
            }
        });

        $this->createdOrderNumber = $orderNumber;
        $this->reset([
            'orderItems', 'deliveryName', 'deliveryPhone', 'deliveryAddress',
            'deliveryCity', 'deliveryRegion', 'notes', 'phoneSearch',
            'foundCustomer', 'customerConfirmed', 'showNewCustomerForm',
            'newName', 'newEmail', 'newPhone',
        ]);
    }

    public function resetAll(): void
    {
        $this->createdOrderNumber = null;
        $this->reset([
            'orderItems', 'deliveryName', 'deliveryPhone', 'deliveryAddress',
            'deliveryCity', 'deliveryRegion', 'notes', 'phoneSearch',
            'foundCustomer', 'customerConfirmed', 'showNewCustomerForm',
            'newName', 'newEmail', 'newPhone', 'productSearch',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.manual-order');
    }
}
