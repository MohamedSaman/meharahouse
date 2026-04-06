<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\WhatsappOrderToken;
use App\Models\Product;
use App\Models\Setting;

#[Title('WhatsApp Orders')]
#[Layout('layouts.admin')]
class WhatsappOrders extends Component
{
    use WithPagination;

    // ── Token List ────────────────────────────────────────────────────
    public string $filterStatus = 'all';
    public string $dateFrom     = '';
    public string $dateTo       = '';

    // ── Generate Modal ────────────────────────────────────────────────
    public bool   $showGenerateModal = false;
    public string $searchProduct     = '';
    public array  $productResults    = [];
    public array  $selectedProducts  = []; // Each: {id, name, price, quantity}
    public string $notes             = '';

    // Customer WhatsApp number
    public string $customerPhone = '';

    // Flash link after generation
    public ?string $generatedLink    = null;
    public ?string $generatedTokenId = null;
    public ?string $generatedPhone   = null;

    // ── Listeners ─────────────────────────────────────────────────────
    protected $listeners = ['closeModal'];

    public function closeModal(): void
    {
        $this->showGenerateModal = false;
    }

    public function updatedDateFrom(): void { $this->resetPage(); }
    public function updatedDateTo(): void   { $this->resetPage(); }

    public function clearDates(): void
    {
        $this->dateFrom = '';
        $this->dateTo   = '';
        $this->resetPage();
    }

    // ── Computed Properties ───────────────────────────────────────────

    #[Computed]
    public function subtotal(): float
    {
        return collect($this->selectedProducts)->sum(function ($p) {
            return (float) $p['price'] * (int) ($p['quantity'] ?? 1);
        });
    }

    #[Computed]
    public function advancePercentage(): int
    {
        return (int) Setting::get('advance_payment_percentage', '50');
    }

    #[Computed]
    public function advanceAmount(): float
    {
        return round($this->subtotal * $this->advancePercentage / 100, 2);
    }

    // ── Product Search ────────────────────────────────────────────────

    public function updatedSearchProduct(): void
    {
        if (strlen($this->searchProduct) < 2) {
            $this->productResults = [];
            return;
        }

        $this->productResults = Product::where('is_active', true)
            ->where('name', 'like', '%' . $this->searchProduct . '%')
            ->limit(8)
            ->get(['id', 'name', 'price', 'sale_price'])
            ->map(function ($product) {
                return [
                    'id'    => $product->id,
                    'name'  => $product->name,
                    'price' => (float) ($product->sale_price ?: $product->price),
                ];
            })
            ->toArray();
    }

    // ── Product Selection ─────────────────────────────────────────────

    public function addProduct(int $productId, string $name, float $price): void
    {
        // Prevent duplicates — increase quantity instead
        foreach ($this->selectedProducts as $index => $product) {
            if ($product['id'] === $productId) {
                $this->selectedProducts[$index]['quantity']++;
                $this->searchProduct  = '';
                $this->productResults = [];
                return;
            }
        }

        $this->selectedProducts[] = [
            'id'       => $productId,
            'name'     => $name,
            'price'    => $price,
            'quantity' => 1,
        ];

        $this->searchProduct  = '';
        $this->productResults = [];
    }

    public function updatePrice(int $index, string $price): void
    {
        $value = max(0, (float) $price);
        if (isset($this->selectedProducts[$index])) {
            $this->selectedProducts[$index]['price'] = $value;
        }
    }

    public function removeProduct(int $index): void
    {
        array_splice($this->selectedProducts, $index, 1);
        $this->selectedProducts = array_values($this->selectedProducts);
    }

    public function updateQuantity(int $index, int $qty): void
    {
        if ($qty < 1) $qty = 1;
        if (isset($this->selectedProducts[$index])) {
            $this->selectedProducts[$index]['quantity'] = $qty;
        }
    }

    // ── Token Generation ──────────────────────────────────────────────

    public function generateToken(): void
    {
        if (empty($this->selectedProducts)) {
            $this->addError('selectedProducts', 'Please select at least one product.');
            return;
        }

        // Build the products array to store in the token
        $products = collect($this->selectedProducts)->map(function ($p) {
            return [
                'product_id'   => $p['id'],
                'product_name' => $p['name'],
                'quantity'     => (int) ($p['quantity'] ?? 1),
                'price'        => (float) $p['price'],
            ];
        })->toArray();

        $subtotal    = $this->subtotal;
        $advancePct  = $this->advancePercentage;
        $advanceAmt  = $this->advanceAmount;

        $token = WhatsappOrderToken::generate(
            adminId:    auth()->id(),
            products:   $products,
            subtotal:   $subtotal,
            advancePct: $advancePct,
            advanceAmt: $advanceAmt,
            notes:      $this->notes,
        );

        $this->generatedLink    = route('whatsapp.order.form', ['token' => $token->token]);
        $this->generatedTokenId = $token->token;
        $this->generatedPhone   = preg_replace('/[^0-9+]/', '', $this->customerPhone) ?: null;

        // Reset modal state
        $this->showGenerateModal = false;
        $this->selectedProducts  = [];
        $this->searchProduct     = '';
        $this->productResults    = [];
        $this->notes             = '';
        $this->customerPhone     = '';
        $this->resetPage();
    }

    public function dismissGeneratedLink(): void
    {
        $this->generatedLink    = null;
        $this->generatedTokenId = null;
        $this->generatedPhone   = null;
    }

    // ── Render ────────────────────────────────────────────────────────

    public function render()
    {
        $tokensQuery = WhatsappOrderToken::with(['createdBy', 'order'])
            ->when($this->filterStatus !== 'all', fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderByDesc('created_at');

        $tokens = $tokensQuery->paginate(20);

        // Stats for the header cards
        $totalGenerated = WhatsappOrderToken::count();
        $totalPending   = WhatsappOrderToken::where('status', 'pending')->count();
        $totalUsed      = WhatsappOrderToken::where('status', 'used')->count();

        return view('livewire.admin.whatsapp-orders', compact(
            'tokens',
            'totalGenerated',
            'totalPending',
            'totalUsed'
        ));
    }
}
