<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Order as OrderModel;
use App\Models\OrderBackorder;
use App\Models\OrderPayment;
use App\Models\Product;
use App\Models\Refund;
use App\Models\Setting;
use App\Services\WhatsappService;
use App\Mail\OrderConfirmed;
use App\Mail\OrderDispatched;
use App\Mail\OrderDelivered;
use App\Mail\PaymentReceived as PaymentReceivedMail;
use App\Mail\RefundProcessed as RefundProcessedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

#[Title('Orders')]
class Order extends Component
{
    use WithPagination;
    use WithFileUploads;

    // ── Filters ───────────────────────────────────────────────────────
    public string $search        = '';
    public string $filterStatus  = '';
    public string $filterSource  = '';
    public string $filterPayment = '';
    public string $dateFrom      = '';
    public string $dateTo        = '';
    public string $sortBy        = 'created_at';
    public string $sortDir       = 'desc';

    // ── Detail Slide-Over Panel ───────────────────────────────────────
    public bool         $showDetail    = false;
    public ?OrderModel  $selectedOrder = null;

    // ── Stock Alert Modal ─────────────────────────────────────────────
    public bool  $showStockAlert      = false;
    public array $stockIssues         = []; // [{item_id, product_id, name, needed, available, short, unit_price, short_amount}]
    public array $stockDecisions      = []; // [index => 'next_batch'|'refund'|'replace']
    public array $stockReplaceChoices = []; // [index => product_id] for 'replace' decisions
    public int   $stockAlertOrder     = 0;
    // Refund sub-confirm inside stock alert
    public bool  $showStockRefundConfirm = false;
    public int   $stockRefundConfirmIdx  = -1;
    // Replace sub-modal inside stock alert
    public bool   $showStockReplaceModal = false;
    public int    $stockReplaceIdx       = -1;
    public string $stockReplaceSearch    = '';

    // ── Refund Modal ──────────────────────────────────────────────────
    public bool   $showRefundModal    = false;
    public int    $refundOrderId      = 0;
    public string $refundAmount       = '';
    public string $refundMethod       = 'bank_transfer';
    public string $refundBankAccount  = '';
    public string $refundReference    = '';
    public string $refundNotes        = '';

    #[Validate('nullable|file|mimes:jpg,jpeg,png,pdf|max:5120')]
    public $refundProofFile = null;

    // ── Backorder / Partial Fulfillment ───────────────────────────────
    public bool  $showBackorderModal = false;
    public int   $backorderOrderId   = 0;
    public array $backorderItems     = []; // [{item_id, product_id, product_name, ordered, available, short, decision}]

    // ─────────────────────────────────────────────────────────────────

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDateFrom(): void { $this->resetPage(); }
    public function updatedDateTo(): void   { $this->resetPage(); }

    public function clearDates(): void
    {
        $this->dateFrom = '';
        $this->dateTo   = '';
        $this->resetPage();
    }

    // ── View Detail Panel ─────────────────────────────────────────────

    public function viewOrder(int $id): void
    {
        $this->selectedOrder = OrderModel::with([
            'user',
            'items.product',
            'payments.confirmedBy',
            'statusLogs.createdBy',
            'refund',
            'refunds',
            'whatsappToken',
            'backorders.replacementProduct',
            'backorders.orderItem',
        ])->findOrFail($id);

        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail    = false;
        $this->selectedOrder = null;
    }

    // ── Status Transitions ────────────────────────────────────────────

    /**
     * Move an order to 'confirmed' — checks stock first, then deducts it.
     */
    public function confirmOrder(int $orderId): void
    {
        $order = OrderModel::with(['items.product', 'payments'])->findOrFail($orderId);

        // Payment warning — show modal if no confirmed payment, let admin decide
        $hasPaid = $order->payments
            ->whereIn('type', ['advance', 'full', 'balance'])
            ->where('status', 'confirmed')
            ->isNotEmpty();
        if (!$hasPaid) {
            $this->dispatch('no-payment-on-confirm', [
                'orderId'  => $orderId,
                'orderNum' => $order->order_number,
            ]);
            return;
        }

        // ── Stock check ───────────────────────────────────────────────
        $issues = [];
        foreach ($order->items as $item) {
            $product = $item->product;
            if (!$product) continue;
            if ($product->stock < $item->quantity) {
                $short = (int) $item->quantity - (int) $product->stock;
                $issues[] = [
                    'item_id'      => $item->id,
                    'product_id'   => $item->product_id,
                    'name'         => $item->product_name,
                    'size'         => $item->size,
                    'needed'       => (int) $item->quantity,
                    'available'    => (int) $product->stock,
                    'short'        => $short,
                    'unit_price'   => (float) $item->price,
                    'short_amount' => round((float) $item->price * $short, 2),
                ];
            }
        }

        if (!empty($issues)) {
            $this->stockIssues     = $issues;
            $this->stockDecisions  = array_fill(0, count($issues), 'next_batch');
            $this->stockAlertOrder = $orderId;
            $this->showStockAlert  = true;
            return;
        }

        // ── Deduct stock & confirm ────────────────────────────────────
        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->decrement('stock', $item->quantity);
            }
        }

        $order->logStatus('confirmed', 'Order confirmed by admin. Stock deducted.', auth()->id());
        $order->update(['status' => 'confirmed']);
        $fresh = $order->fresh(['items']);
        try { WhatsappService::orderConfirmed($fresh); } catch (\Throwable) {}
        try {
            $email = $fresh->shipping_address['email'] ?? ($fresh->user?->email ?? null);
            if ($email) Mail::to($email)->send(new OrderConfirmed($fresh));
        } catch (\Throwable) {}
        $this->refreshSelectedOrder($orderId);
        session()->flash('success', 'Order confirmed and stock updated.');
    }

    public function closeStockAlert(): void
    {
        $this->showStockAlert          = false;
        $this->stockIssues             = [];
        $this->stockDecisions          = [];
        $this->stockReplaceChoices     = [];
        $this->stockAlertOrder         = 0;
        $this->showStockRefundConfirm  = false;
        $this->stockRefundConfirmIdx   = -1;
        $this->showStockReplaceModal   = false;
        $this->stockReplaceIdx         = -1;
        $this->stockReplaceSearch      = '';
    }

    /**
     * Confirm order even though no payment is on record (admin override).
     */
    public function confirmOrderAnyway(int $orderId): void
    {
        $order = OrderModel::with('items.product')->findOrFail($orderId);
        // Skip payment check — proceed directly to stock check & confirm
        $issues = [];
        foreach ($order->items as $item) {
            $product = $item->product;
            if (!$product) continue;
            if ($product->stock < $item->quantity) {
                $short = (int) $item->quantity - (int) $product->stock;
                $issues[] = [
                    'item_id'      => $item->id,
                    'product_id'   => $item->product_id,
                    'name'         => $item->product_name,
                    'size'         => $item->size,
                    'needed'       => (int) $item->quantity,
                    'available'    => (int) $product->stock,
                    'short'        => $short,
                    'unit_price'   => (float) $item->price,
                    'short_amount' => round((float) $item->price * $short, 2),
                ];
            }
        }
        if (!empty($issues)) {
            $this->stockIssues     = $issues;
            $this->stockDecisions  = array_fill(0, count($issues), 'next_batch');
            $this->stockAlertOrder = $orderId;
            $this->showStockAlert  = true;
            return;
        }
        foreach ($order->items as $item) {
            if ($item->product) $item->product->decrement('stock', $item->quantity);
        }
        $order->logStatus('confirmed', 'Order confirmed (no payment recorded — admin override).', auth()->id());
        $order->update(['status' => 'confirmed']);
        $this->refreshSelectedOrder($orderId);
        session()->flash('success', 'Order confirmed (payment still pending — collect before dispatch).');
    }

    /**
     * Select next_batch for an item (direct toggle, no sub-popup needed).
     */
    public function setStockNextBatch(int $index): void
    {
        $this->showStockRefundConfirm = false;
        $this->stockRefundConfirmIdx  = -1;
        $this->stockDecisions[$index] = 'next_batch';
    }

    /**
     * Open the refund sub-confirm popup for a specific item and mark it as 'refund'.
     */
    public function openStockRefundConfirm(int $index): void
    {
        $this->stockDecisions[$index]  = 'refund';
        $this->stockRefundConfirmIdx   = $index;
        $this->showStockRefundConfirm  = true;
    }

    /** Close just the refund sub-confirm (revert to next_batch for that item). */
    public function closeStockRefundConfirm(): void
    {
        if ($this->stockRefundConfirmIdx >= 0) {
            $this->stockDecisions[$this->stockRefundConfirmIdx] = 'next_batch';
        }
        $this->showStockRefundConfirm = false;
        $this->stockRefundConfirmIdx  = -1;
    }

    /** Confirm the refund selection for the item and close the sub-popup. */
    public function confirmStockRefundItem(): void
    {
        $this->showStockRefundConfirm = false;
        $this->stockRefundConfirmIdx  = -1;
    }

    /** Open the replace sub-modal for a specific stock issue item. */
    public function openStockReplaceModal(int $index): void
    {
        $this->showStockRefundConfirm  = false;
        $this->stockRefundConfirmIdx   = -1;
        $this->stockReplaceIdx         = $index;
        $this->stockReplaceSearch      = '';
        $this->stockDecisions[$index]  = 'replace';
        $this->showStockReplaceModal   = true;
    }

    /** Cancel the replace sub-modal — revert to next_batch. */
    public function closeStockReplaceModal(): void
    {
        if ($this->stockReplaceIdx >= 0 && !isset($this->stockReplaceChoices[$this->stockReplaceIdx])) {
            $this->stockDecisions[$this->stockReplaceIdx] = 'next_batch';
        }
        $this->showStockReplaceModal = false;
        $this->stockReplaceIdx       = -1;
        $this->stockReplaceSearch    = '';
    }

    /** Confirm the replacement product selection. */
    public function confirmStockReplaceItem(int $productId): void
    {
        $product = \App\Models\Product::find($productId);
        if (!$product) return;

        $idx   = $this->stockReplaceIdx;
        $short = (int) ($this->stockIssues[$idx]['short'] ?? 0);

        if (($product->stock ?? 0) < $short) {
            session()->flash('replace_error', "Insufficient stock for " . $product->name . ". Needed: {$short}, Available: " . ($product->stock ?? 0));
            return;
        }

        $this->stockDecisions[$idx]    = 'replace';
        $this->stockReplaceChoices[$idx] = $productId;
        $this->showStockReplaceModal   = false;
        $this->stockReplaceIdx         = -1;
        $this->stockReplaceSearch      = '';
    }

    /**
     * Apply per-item decisions from the stock alert modal.
     *
     * next_batch → creates a backorder record; deducts available stock and confirms order
     * refund     → reduces order item amount by short qty × unit price, confirms with available stock
     */
    public function applyStockDecisions(): void
    {
        if (!$this->stockAlertOrder) return;

        $order = OrderModel::with('items.product')->findOrFail($this->stockAlertOrder);

        $partialRefundAmount = 0.0;

        foreach ($this->stockIssues as $index => $issue) {
            $decision = $this->stockDecisions[$index] ?? 'next_batch';

            if ($decision === 'next_batch') {
                OrderBackorder::create([
                    'order_id'      => $order->id,
                    'order_item_id' => $issue['item_id'],
                    'product_id'    => $issue['product_id'],
                    'product_name'  => $issue['name'],
                    'size'          => $issue['size'] ?? null,
                    'ordered_qty'   => $issue['needed'],
                    'available_qty' => $issue['available'],
                    'short_qty'     => $issue['short'],
                    'decision'      => 'repurchase',
                    'status'        => 'repurchasing',
                    'created_by'    => auth()->id(),
                ]);

                // Mark the order item as backordered so its history is preserved
                $orderItem = $order->items->firstWhere('id', $issue['item_id']);
                if ($orderItem) {
                    $orderItem->update([
                        'status'                    => 'backordered',
                        'original_qty'              => $orderItem->original_qty ?? $issue['needed'],
                        'original_ordered_subtotal' => $orderItem->original_ordered_subtotal
                                                        ?? round($issue['unit_price'] * $issue['needed'], 2),
                    ]);
                }

            } elseif ($decision === 'replace') {
                $replacementProductId = $this->stockReplaceChoices[$index] ?? null;
                $replacementProduct   = $replacementProductId ? \App\Models\Product::find($replacementProductId) : null;

                if (!$replacementProduct) {
                    // No replacement chosen yet — skip, leave item as-is
                    continue;
                }

                $newQty = $issue['short']; // the short quantity being replaced

                // Final safety check: ensure the replacement product STILL has enough stock
                if ($replacementProduct->stock < $newQty) {
                    session()->flash('error', "Stock for replacement product ({$replacementProduct->name}) was sold out. Cannot confirm order.");
                    return; // Stop the entire process so admin can pick another replacement
                }

                $orderItem = $order->items->firstWhere('id', $issue['item_id']);
                if ($orderItem) {
                    $newPrice    = (float) $replacementProduct->price;
                    $newSubtotal = round($newPrice * $newQty, 2);

                    $orderItem->update([
                        'product_id'                => $replacementProduct->id,
                        'product_name'              => $replacementProduct->name,
                        'price'                     => $newPrice,
                        'quantity'                  => $newQty,
                        'subtotal'                  => $newSubtotal,
                        'status'                    => 'replaced',
                        'original_qty'              => $issue['needed'],
                        'original_ordered_subtotal' => round($issue['unit_price'] * $issue['needed'], 2),
                        'is_replaced'               => true,
                        'original_product_id'       => $issue['product_id'],
                        'original_product_name'     => $issue['name'],
                        'original_price'            => $issue['unit_price'],
                        'original_subtotal'         => round($issue['unit_price'] * $newQty, 2),
                        'replacement_notes'         => null,
                        'replaced_at'               => now(),
                        'replaced_by'               => auth()->id(),
                    ]);
                }

            } elseif ($decision === 'refund') {
                $shortAmount          = round($issue['unit_price'] * $issue['short'], 2);
                $partialRefundAmount += $shortAmount;

                $orderItem = $order->items->firstWhere('id', $issue['item_id']);
                if ($orderItem) {
                    if ($issue['available'] > 0) {
                        // Partial refund: reduce quantity to what is actually available,
                        // keep the item active for the fulfilled portion
                        $orderItem->update([
                            'status'                    => 'active',
                            'quantity'                  => $issue['available'],
                            'subtotal'                  => round($issue['unit_price'] * $issue['available'], 2),
                            'original_qty'              => $issue['needed'],
                            'original_ordered_subtotal' => round($issue['unit_price'] * $issue['needed'], 2),
                            'refund_amount'             => $shortAmount,
                        ]);
                    } else {
                        // Full refund: keep the record but mark as refunded — do NOT delete
                        $orderItem->update([
                            'status'                    => 'refunded',
                            'quantity'                  => 0,
                            'subtotal'                  => 0,
                            'original_qty'              => $issue['needed'],
                            'original_ordered_subtotal' => round($issue['unit_price'] * $issue['needed'], 2),
                            'refund_amount'             => $shortAmount,
                        ]);
                    }
                }
            }
        }

        // Check if any prices changed due to replacement (not just refunds)
        $hasReplacement = collect($this->stockDecisions)->contains('replace');

        // Recalculate order total if any refunds or replacements applied.
        // Exclude fully-refunded items (status = 'refunded') from the subtotal.
        if ($partialRefundAmount > 0 || $hasReplacement) {
            $order->refresh()->load('items');
            $newSubtotal = $order->items->where('status', '!=', 'refunded')->sum('subtotal');

            if ($newSubtotal > 0) {
                // Recalculate tax based on new subtotal
                $taxRate = (float) \App\Models\Setting::get('tax_rate', '15') / 100;
                $newTax  = round($newSubtotal * $taxRate, 2);

                // Recalculate percentage-based coupon discount if applicable
                $newDiscount = (float) $order->discount;
                if ($order->coupon_code) {
                    $coupon = \App\Models\Coupon::where('code', $order->coupon_code)->first();
                    if ($coupon && $coupon->type === 'percentage') {
                        $newDiscount = round($newSubtotal * ($coupon->value / 100), 2);
                        if ($coupon->max_discount && $newDiscount > $coupon->max_discount) {
                            $newDiscount = (float) $coupon->max_discount;
                        }
                    }
                }

                $newTotal = round($newSubtotal + $order->shipping_cost + $newTax - $newDiscount, 2);

                // Recalculate advance/balance split based on the advance percentage
                $advPct           = (float) ($order->advance_percentage ?? 0);
                $newAdvance       = $advPct > 0 ? round($newTotal * $advPct / 100, 2) : (float) $order->advance_amount;

                // Total confirmed payments (advance + balance + full)
                $totalPaid = (float) $order->payments()
                    ->whereIn('type', ['advance', 'balance', 'full'])
                    ->where('status', 'confirmed')
                    ->sum('amount');
                $totalRefunded = (float) $order->refunds()->sum('amount');
                $newBalanceDue = max(0, round($newTotal - $totalPaid + $totalRefunded, 2));

                $order->update([
                    'subtotal'       => $newSubtotal,
                    'tax'            => $newTax,
                    'discount'       => $newDiscount,
                    'total'          => $newTotal,
                    'advance_amount' => $newAdvance,
                    'balance_amount' => $newBalanceDue,
                ]);
            } else {
                // All items refunded — set total to 0 so overpayment calc gives
                // the full paid amount as refundable.
                $order->update([
                    'subtotal'       => 0,
                    'tax'            => 0,
                    'total'          => 0,
                    'balance_amount' => 0,
                ]);
            }
        }

        // Deduct available stock for every non-refunded item
        $order->refresh()->load('items.product');
        $totalDeducted = 0;
        foreach ($order->items->where('status', '!=', 'refunded') as $item) {
            $product = $item->product;
            if (!$product) continue;
            $deduct = min((int) $product->stock, (int) $item->quantity);
            if ($deduct > 0) {
                $product->decrement('stock', $deduct);
                $totalDeducted += $deduct;
            }
        }

        // Determine correct order status after decisions are applied.
        $issuedItemIds     = array_column($this->stockIssues, 'item_id');
        $hasNonIssuedItems = $order->items->whereNotIn('id', $issuedItemIds)->where('status', '!=', 'refunded')->isNotEmpty();
        $sumNextBatch      = collect($this->stockDecisions)->filter(fn($d) => $d === 'next_batch')->count();
        $sumRefundDec      = collect($this->stockDecisions)->filter(fn($d) => $d === 'refund')->count();

        // Truly fully-backordered = no non-issued active items AND nothing deducted AND no refunds
        $fullyBackordered = !$hasNonIssuedItems && $totalDeducted === 0 && $sumRefundDec === 0;

        // All items fully refunded (quantity = 0) = mark order as refunded
        $allRefunded = $order->items->where('status', 'refunded')->count() === $order->items->count()
                       && $order->items->isNotEmpty()
                       && $sumRefundDec > 0
                       && $sumNextBatch === 0;

        if ($allRefunded) {
            $targetStatus = 'refunded';
        } elseif ($fullyBackordered) {
            $targetStatus = 'sourcing';
        } else {
            $targetStatus = 'confirmed';
        }

        $notes = match($targetStatus) {
            'refunded' => 'All items refunded — order fully refunded.',
            'sourcing'  => 'All items on backorder — awaiting stock from next batch.',
            default     => 'Order confirmed with partial stock.',
        };
        if ($partialRefundAmount > 0 && $targetStatus !== 'refunded') {
            $notes .= ' Rs. ' . number_format($partialRefundAmount, 0) . ' refunded for short items.';
        }
        $order->logStatus($targetStatus, $notes, auth()->id());
        $order->update(['status' => $targetStatus]);

        // Capture replace count before closeStockAlert() resets stockDecisions
        $sumReplace = collect($this->stockDecisions)->filter(fn($d) => $d === 'replace')->count();

        $this->closeStockAlert();
        $this->refreshSelectedOrder($order->id);

        if ($targetStatus === 'refunded') {
            $msg = 'All items refunded. Please fill in the refund payment details below.';
        } elseif ($fullyBackordered) {
            $msg = 'All items backordered — order marked as Sourcing. Check Backorders page.';
        } else {
            $msg = 'Order confirmed.';
            if ($partialRefundAmount > 0) {
                // Calculate actual refund (overpayment) for the message
                $order->load('payments');
                $paidSoFar    = $order->totalPaid();
                $currentTotal = (float) $order->total;
                $actualRefund = max(0, round($paidSoFar - $currentTotal, 2));
                if ($actualRefund > 0) {
                    $msg .= ' Item value cancelled: Rs. ' . number_format($partialRefundAmount, 0)
                          . '. Refund to customer: Rs. ' . number_format($actualRefund, 0)
                          . ' (overpayment) — please fill in the refund details below.';
                } else {
                    $msg .= ' Item value cancelled: Rs. ' . number_format($partialRefundAmount, 0)
                          . '. No cash refund needed — customer still owes Rs. ' . number_format($currentTotal - $paidSoFar, 0) . '.';
                }
            }
            if ($sumReplace > 0) $msg .= " {$sumReplace} item(s) replaced directly in the order — no backorder created.";
            if ($partialRefundAmount === 0.0 && $sumReplace === 0) $msg .= ' Backorders created for short items.';
        }
        session()->flash('success', $msg);

        // If any items were refunded, open the refund modal so admin can
        // record the payment method, bank account, reference and proof.
        if ($partialRefundAmount > 0) {
            // The refund to the customer is the OVERPAYMENT — the amount they paid
            // beyond the new (reduced) order total. NOT the full item price.
            //
            // Example: Order Rs.2,575, customer paid Rs.1,288 advance (50%).
            //          p2 (Rs.1,575) refunded → new total = Rs.1,000
            //          Overpayment = Rs.1,288 - Rs.1,000 = Rs.288 (refund this)
            //          NOT min(1575, 1288) = Rs.1,288 — that's wrong!
            $order->load('payments');
            $totalPaid       = $order->totalPaid();
            $newOrderTotal   = (float) $order->total;  // Already updated above
            $overpayment     = max(0, round($totalPaid - $newOrderTotal, 2));

            // Only show refund modal if customer actually overpaid
            if ($overpayment > 0) {
                $this->refundOrderId     = $order->id;
                $this->refundAmount      = (string) $overpayment;
                $this->refundMethod      = 'bank_transfer';
                $this->refundBankAccount = '';
                $this->refundReference   = '';
                $this->refundNotes       = 'Partial refund for out-of-stock item(s). Item value: Rs. ' . number_format($partialRefundAmount, 0) . '. Overpayment refund: Rs. ' . number_format($overpayment, 0) . '.';
                $this->refundProofFile   = null;
                $this->showRefundModal   = true;
            }
            // If overpayment = 0 (customer paid less than new total), no refund needed
            // — balance_amount is already updated above to reflect what they still owe.
        }
    }

    /**
     * Mark an order as being sourced from supplier.
     */

    /**
     * Mark that the supplier has delivered the product to the store.
     */

    // ── Backorder / Partial Fulfillment ──────────────────────────────

    /**
     * Open the partial-fulfillment / backorder modal for an order.
     * Computes the stock shortage per item and pre-fills the modal.
     */
    public function openBackorderModal(int $orderId): void
    {
        $order = OrderModel::with('items.product')->findOrFail($orderId);
        $items = [];

        foreach ($order->items as $item) {
            $stock  = (int) ($item->product?->stock ?? 0);
            $needed = (int) $item->quantity;
            if ($stock < $needed) {
                $items[] = [
                    'item_id'      => $item->id,
                    'product_id'   => $item->product_id,
                    'product_name' => $item->product_name,
                    'ordered'      => $needed,
                    'available'    => $stock,
                    'short'        => $needed - $stock,
                    'decision'     => 'repurchase', // default decision
                ];
            }
        }

        if (empty($items)) {
            session()->flash('success', 'No stock shortage detected — all items have sufficient stock.');
            return;
        }

        $this->backorderOrderId   = $orderId;
        $this->backorderItems     = $items;
        $this->showBackorderModal = true;
    }

    /**
     * Update a single item's backorder decision (repurchase / waitlist) in the modal array.
     */
    public function setBackorderDecision(int $index, string $decision): void
    {
        if (isset($this->backorderItems[$index])) {
            $this->backorderItems[$index]['decision'] = $decision;
        }
    }

    /**
     * Persist backorder decisions, deduct the available stock, and move the
     * order to confirmed so it can be dispatched with what is in stock.
     */
    public function processBackorder(): void
    {
        if (empty($this->backorderItems) || !$this->backorderOrderId) return;

        $order = OrderModel::with('items.product')->findOrFail($this->backorderOrderId);

        // Create one backorder record per short item
        foreach ($this->backorderItems as $item) {
            OrderBackorder::create([
                'order_id'      => $order->id,
                'order_item_id' => $item['item_id'],
                'product_id'    => $item['product_id'],
                'product_name'  => $item['product_name'],
                'ordered_qty'   => $item['ordered'],
                'available_qty' => $item['available'],
                'short_qty'     => $item['short'],
                'decision'      => $item['decision'],
                'status'        => $item['decision'] === 'repurchase' ? 'repurchasing' : 'pending',
                'created_by'    => auth()->id(),
            ]);
        }

        // Deduct only the available (not the full ordered) qty from stock
        foreach ($order->items as $orderItem) {
            $stock  = (int) ($orderItem->product?->stock ?? 0);
            $deduct = min($stock, (int) $orderItem->quantity);
            if ($deduct > 0 && $orderItem->product) {
                $orderItem->product->decrement('stock', $deduct);
            }
        }

        // Confirm the order so staff can dispatch the available portion
        $order->logStatus(
            'confirmed',
            'Partial fulfillment acknowledged. Backorder created for the shortage.',
            auth()->id()
        );
        $order->update(['status' => 'confirmed', 'supplier_status' => 'received']);

        $this->showBackorderModal = false;
        $this->backorderItems     = [];
        $this->backorderOrderId   = 0;

        $this->refreshSelectedOrder($order->id);
        session()->flash('success', 'Backorder recorded. Order confirmed for dispatch with available stock.');
    }

    /**
     * Mark a specific backorder record as ready from the order detail panel.
     * Full dispatch lifecycle (dispatched → delivered → completed) is managed
     * from the dedicated Back Orders page.
     */
    public function fulfillBackorder(int $backorderId): void
    {
        $backorder = OrderBackorder::findOrFail($backorderId);
        $backorder->update(['status' => 'ready']);

        $this->refreshSelectedOrder($backorder->order_id);
        session()->flash('success', 'Backorder marked as ready. Go to Back Orders to dispatch it.');
    }

    /**
     * Manual override to force dispatch an order even if it has an outstanding balance.
     * This should only be used in exceptional cases.
     */
    public function forceDispatch(int $orderId): void
    {
        $order = OrderModel::findOrFail($orderId);
        
        // Strict gate: confirm balance is 0 or user specifically authorized (though here we just gate it)
        $due = $order->balanceDue();
        if ($due > 0) {
            session()->flash('error', "Cannot force dispatch. Order MH-{$order->order_number} still has LKR " . number_format($due, 2) . " due.");
            return;
        }

        $order->logStatus('dispatched', 'Order force-dispatched by admin.', auth()->id());
        $order->update(['status' => 'dispatched']);
        
        $fresh = $order->fresh(['shipmentBatch']);
        try { WhatsappService::orderDispatched($fresh); } catch (\Throwable) {}
        
        $this->refreshSelectedOrder($orderId);
        session()->flash('success', "Order MH-{$order->order_number} force-dispatched.");
    }
    /**
     * Manual override to force delivery even if balance is outstanding.
     */
    public function forceDeliver(int $orderId): void
    {
        $order = OrderModel::findOrFail($orderId);
        
        $due = $order->balanceDue();
        if ($due > 0) {
            session()->flash('error', "Cannot force delivery. Order MH-{$order->order_number} still has LKR " . number_format($due, 2) . " due.");
            return;
        }

        $order->logStatus('delivered', 'Order marked delivered via force manual override.', auth()->id());
        $order->update(['status' => 'delivered']);
        
        $fresh = $order->fresh();
        try { WhatsappService::orderDelivered($fresh); } catch (\Throwable) {}
        try {
            $email = $fresh->shipping_address['email'] ?? ($fresh->user?->email ?? null);
            if ($email) Mail::to($email)->send(new OrderDelivered($fresh));
        } catch (\Throwable) {}
        $this->refreshSelectedOrder($orderId);
        session()->flash('success', "Order MH-{$order->order_number} marked as delivered.");
    }

    /**
     * Manual override to force completion.
     */
    public function forceComplete(int $orderId): void
    {
        $order = OrderModel::findOrFail($orderId);
        
        $due = $order->balanceDue();
        if ($due > 0) {
            session()->flash('error', "Cannot mark as completed. Order MH-{$order->order_number} still has LKR " . number_format($due, 2) . " due.");
            return;
        }

        $order->logStatus('completed', 'Order force-completed by admin.', auth()->id());
        $order->update(['status' => 'completed', 'payment_status' => 'paid']);
        try { WhatsappService::orderCompleted($order->fresh()); } catch (\Throwable) {}
        $this->refreshSelectedOrder($orderId);
        session()->flash('success', "Order MH-{$order->order_number} marked as completed.");
    }

    // ── Payment Actions ───────────────────────────────────────────────

    /**
     * Confirm an advance payment receipt — moves order to 'payment_received'.
     */
    public function confirmPayment(int $paymentId): void
    {
        $payment = OrderPayment::with('order.items')->findOrFail($paymentId);
        $order   = $payment->order;

        $payment->update([
            'status'       => 'confirmed',
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);

        // Advance order status / payment_status based on payment type
        if (in_array($order->status, ['new', 'payment_received'])) {
            if ($payment->type === 'full') {
                // Full payment confirmed — mark paid immediately
                $order->logStatus('payment_received', 'Full payment confirmed by admin.', auth()->id());
                $order->update(['status' => 'payment_received', 'payment_status' => 'paid']);
            } elseif ($payment->type === 'advance' && $order->status === 'new') {
                // Advance deposit confirmed — order moves to payment_received, balance still outstanding
                $order->logStatus('payment_received', 'Advance payment confirmed by admin.', auth()->id());
                $order->update(['status' => 'payment_received', 'payment_status' => 'partial']);
            } elseif ($payment->type === 'balance') {
                // Balance payment confirmed — recalculate total paid and update balance_amount
                // BUG-09 fix: Account for refunds when calculating remaining balance
                $totalConfirmed = $order->payments()->where('status', 'confirmed')
                    ->whereIn('type', ['advance', 'balance', 'full'])
                    ->sum('amount');
                $totalRefunded  = (float) $order->refunds()->sum('amount');
                $newBalance     = max(0, (float) $order->total - $totalConfirmed + $totalRefunded);
                $paymentStatus  = $newBalance <= 0 ? 'paid' : 'partial';
                $order->update(['payment_status' => $paymentStatus, 'balance_amount' => $newBalance]);
                if ($paymentStatus === 'paid') {
                    $order->logStatus($order->status, 'Balance payment confirmed. Order fully paid.', auth()->id());
                }
            }
        }

        try { WhatsappService::paymentReceived($order->fresh(), (float) $payment->amount); } catch (\Throwable) {}

        // Build WhatsApp confirmation message for admin to send to customer
        $address  = $order->shipping_address ?? [];
        $phone    = preg_replace('/[^0-9+]/', '', $address['phone'] ?? '');
        $siteName = \App\Models\Setting::get('site_name', 'Meharahouse');

        if ($phone) {
            $items = $order->items->map(fn($i) => "• {$i->product_name} x{$i->quantity}")->implode("\n");
            $msg = "✅ *Payment Confirmed — {$siteName}*\n\n"
                 . "Dear {$address['full_name']},\n\n"
                 . "Your payment has been received and confirmed! 🎉\n\n"
                 . "📦 *Order No:* {$order->order_number}\n"
                 . "💰 *Amount Paid:* Rs. " . number_format($payment->amount, 0) . "\n\n"
                 . "*Items:*\n{$items}\n\n"
                 . "We are now processing your order and will update you once it is dispatched.\n\n"
                 . "Thank you for shopping with {$siteName}! 🙏";

            $this->dispatch('open-whatsapp-prompt', phone: $phone, message: $msg);
        }

        $this->refreshSelectedOrder($order->id);
        session()->flash('success', 'Payment confirmed.');
    }

    /**
     * Reject a payment receipt — marks it rejected, order stays in current status.
     */
    public function rejectPayment(int $paymentId): void
    {
        $payment = OrderPayment::findOrFail($paymentId);
        $payment->update(['status' => 'rejected']);
        $this->refreshSelectedOrder($payment->order_id);
        session()->flash('success', 'Payment receipt rejected. Customer should re-upload.');
    }

    // ── Refund Processing ─────────────────────────────────────────────

    public function openRefundModal(int $orderId): void
    {
        $order = OrderModel::with(['payments', 'refunds'])->findOrFail($orderId);
        $this->refundOrderId      = $orderId;
        // BUG-13 fix: For COD orders where no payment is recorded in the system,
        // allow refund up to the order total (admin manually tracks COD payments).
        $totalPaid                = $order->totalPaid();
        $totalRefunded            = $order->totalRefunded();
        if ($totalPaid <= 0 && $order->payment_method === 'cash_on_delivery') {
            // COD order: assume customer paid the full amount on delivery
            $maxRefundable = max(0, (float) $order->total - $totalRefunded);
        } else {
            $maxRefundable = max(0, $totalPaid - $totalRefunded);
        }
        $this->refundAmount       = (string) $maxRefundable;
        $this->refundMethod       = 'bank_transfer';
        $this->refundBankAccount  = '';
        $this->refundReference    = '';
        $this->refundNotes        = '';
        $this->refundProofFile    = null;
        $this->showRefundModal    = true;
    }

    public function processRefund(): void
    {
        $order = OrderModel::with(['payments', 'refunds'])->findOrFail($this->refundOrderId);

        // Guard against double-submit: if a refund was already recorded for this
        // exact amount within the last 10 seconds, silently close and bail out.
        $alreadyRefunded = $order->refunds()
            ->where('amount', (float) $this->refundAmount)
            ->where('created_at', '>=', now()->subSeconds(10))
            ->exists();
        if ($alreadyRefunded) {
            $this->showRefundModal = false;
            return;
        }

        // BUG-13 fix: For COD orders, allow refund up to order total
        $totalPaid     = $order->totalPaid();
        $totalRefunded = $order->totalRefunded();
        if ($totalPaid <= 0 && $order->payment_method === 'cash_on_delivery') {
            $maxRefundable = max(0, (float) $order->total - $totalRefunded);
        } else {
            $maxRefundable = max(0, $totalPaid - $totalRefunded);
        }

        $this->validate([
            'refundAmount' => ['required', 'numeric', 'min:0.01', 'max:' . $maxRefundable],
            'refundNotes'  => ['nullable', 'string', 'max:1000'],
        ]);

        $refund = Refund::create([
            'order_id'     => $order->id,
            'customer_id'  => $order->user_id,
            'amount'       => $this->refundAmount,
            'method'       => 'bank_transfer', // placeholder — updated when payment is processed on Refunds page
            'notes'        => $this->refundNotes ?: null,
            'status'       => 'pending',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        // Reduce balance_amount by the refund amount so balanceDue() reflects the refund
        $newBalance = max(0, (float) $order->balance_amount - (float) $this->refundAmount);

        // Only mark the entire order as 'refunded' if ALL items are refunded.
        // If just some items are refunded the order stays 'confirmed' so delivery can continue.
        $order->load('items');
        $allItemsRefunded = $order->items->isNotEmpty()
            && $order->items->every(fn($i) => $i->status === 'refunded');
        $newOrderStatus   = $allItemsRefunded ? 'refunded' : $order->status;
        $newPaymentStatus = $allItemsRefunded ? 'refunded' : $order->payment_status;

        $order->logStatus($newOrderStatus, 'Refund of Rs. ' . number_format($this->refundAmount, 0) . ' processed.', auth()->id());
        $order->update([
            'status'         => $newOrderStatus,
            'payment_status' => $newPaymentStatus,
            'balance_amount' => $newBalance,
        ]);

        // Notify the customer via email
        try {
            $fresh = $order->fresh();
            $email = $fresh->shipping_address['email'] ?? ($fresh->user?->email ?? null);
            if ($email) Mail::to($email)->send(new RefundProcessedMail($fresh, $refund));
        } catch (\Throwable) {}

        // Build WhatsApp refund notification message for admin to send
        $address  = $order->shipping_address ?? [];
        $phone    = preg_replace('/[^0-9+]/', '', $address['phone'] ?? '');
        $siteName = Setting::get('site_name', 'Meharahouse');
        if ($phone) {
            $bankNote = $this->refundBankAccount
                ? "\n🏦 Transfer to your account: *{$this->refundBankAccount}*"
                : '';
            $msg = "💸 *Refund Processed — {$siteName}*\n\n"
                 . "Dear {$address['full_name']},\n\n"
                 . "Your refund has been processed for order *{$order->order_number}*.\n\n"
                 . "💰 *Refund Amount:* Rs. " . number_format($this->refundAmount, 0)
                 . $bankNote . "\n\n"
                 . "The amount will be transferred within 3–5 business days.\n\n"
                 . "Thank you for shopping with {$siteName}! 🙏";
            $this->dispatch('open-whatsapp-prompt', phone: $phone, message: $msg);
        }

        $this->showRefundModal = false;
        $this->refreshSelectedOrder($order->id);
        session()->flash('success', 'Refund recorded as pending. Go to Refunds page to process the payment.');
    }

    // ── WhatsApp Balance Reminder ─────────────────────────────────────

    /**
     * Build a ready-to-copy WhatsApp reminder message for the admin to send.
     * The message contains the outstanding balance amount and bank details.
     */
    public function sendBalanceReminder(int $orderId): void
    {
        $order       = OrderModel::findOrFail($orderId);
        $bankDetails = Setting::get('bank_transfer_details', '(Bank details not configured)');
        $balanceDue  = $order->balanceDue();

        $message = "Dear customer, your order *{$order->order_number}* has been delivered.\n\n"
            . "Outstanding balance: *Rs. " . number_format($balanceDue, 0) . "*\n\n"
            . "Please transfer to:\n{$bankDetails}\n\n"
            . "Thank you for shopping with Meharahouse!";

        // Flash the message for the admin to copy and paste into WhatsApp
        session()->flash('whatsapp_reminder', $message);
        session()->flash('whatsapp_reminder_order', $order->order_number);
    }

    // ── CSV Export ───────────────────────────────────────────────────

    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $orders = OrderModel::with(['user', 'items', 'payments'])
            ->when($this->search, function ($q) {
                $q->where('order_number', 'like', "%{$this->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%"));
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterSource, fn($q) => $q->where('source', $this->filterSource))
            ->when($this->filterPayment, fn($q) => $q->where('payment_status', $this->filterPayment))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy($this->sortBy, $this->sortDir)
            ->get();

        $filename = 'orders-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($orders) {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, [
                'Order #', 'Date', 'Customer Name', 'Phone', 'Alt Phone',
                'WhatsApp Tag (Last 4)', 'City', 'District', 'Address',
                'Abaya Size', 'Abaya Model',
                'Items', 'Subtotal', 'Advance', 'Balance Due',
                'Total', 'Payment Status', 'Order Status', 'Source',
                'Waybill #', 'Delivery Agent', 'Notes'
            ]);

            foreach ($orders as $order) {
                $addr    = $order->shipping_address ?? [];
                $phone   = $addr['phone'] ?? '';
                $last4   = strlen(preg_replace('/[^0-9]/', '', $phone)) >= 4
                            ? substr(preg_replace('/[^0-9]/', '', $phone), -4)
                            : '';
                $items   = $order->items->map(fn($i) => $i->product_name . ' x' . $i->quantity)->implode(' | ');
                $balance = $order->balanceDue();

                fputcsv($handle, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i'),
                    $addr['full_name'] ?? ($order->user?->name ?? ''),
                    $phone,
                    $addr['alt_phone'] ?? '',
                    $last4,
                    $addr['city'] ?? '',
                    $addr['district'] ?? '',
                    $addr['address'] ?? '',
                    $addr['abaya_size'] ?? '',
                    $addr['abaya_model'] ?? '',
                    $items,
                    $order->subtotal,
                    $order->advance_amount,
                    $balance,
                    $order->total,
                    $order->payment_status,
                    $order->status,
                    $order->source,
                    $order->waybill_number ?? '',
                    $order->delivery_agent ?? '',
                    $order->notes ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    // ── Legacy status update (kept for compatibility) ─────────────────

    public function updateStatus(int $id, string $status): void
    {
        $allowed = ['new', 'payment_received', 'confirmed', 'sourcing', 'dispatched', 'delivered', 'completed', 'refunded', 'cancelled'];
        if (!in_array($status, $allowed)) return;

        $order = OrderModel::findOrFail($id);
        $order->logStatus($status, 'Status updated manually by admin.', auth()->id());
        $order->update(['status' => $status]);
        $this->refreshSelectedOrder($id);
        session()->flash('success', 'Order status updated to ' . ucfirst($status) . '.');
    }

    public function updatePaymentStatus(int $id, string $status): void
    {
        $allowed = ['pending', 'partial', 'paid', 'failed', 'refunded'];
        if (!in_array($status, $allowed)) return;

        OrderModel::findOrFail($id)->update(['payment_status' => $status]);
        $this->refreshSelectedOrder($id);
        session()->flash('success', 'Payment status updated.');
    }

    // ── Helper: Refresh Selected Order ───────────────────────────────

    private function refreshSelectedOrder(int $orderId): void
    {
        if ($this->selectedOrder && $this->selectedOrder->id === $orderId) {
            $this->selectedOrder = OrderModel::with([
                'user',
                'items.product',
                'payments.confirmedBy',
                'statusLogs.createdBy',
                'refund',
                'refunds',
                'whatsappToken',
                'backorders.replacementProduct',
                'backorders.orderItem',
            ])->find($orderId);
        }
    }

    // ── Render ────────────────────────────────────────────────────────

    public function render()
    {
        $orders = OrderModel::with('user')
            ->when($this->search, function ($q) {
                $q->where('order_number', 'like', "%{$this->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%"))
                  ->orWhere(function ($q) {
                      // Also search guest orders by shipping address name/phone
                      $q->whereJsonContains('shipping_address->full_name', $this->search)
                        ->orWhereJsonContains('shipping_address->phone', $this->search);
                  });
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterSource, fn($q) => $q->where('source', $this->filterSource))
            ->when($this->filterPayment, fn($q) => $q->where('payment_status', $this->filterPayment))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate(20);

        // Count by the new status values
        $statusCounts = OrderModel::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Count pending receipt confirmations (advance payments awaiting review)
        $pendingReceiptCount = OrderPayment::where('status', 'pending')
            ->whereNotNull('receipt_path')
            ->count();

        // Products for stock-alert replace search
        $stockReplaceProducts = ($this->showStockReplaceModal && strlen($this->stockReplaceSearch) >= 2)
            ? \App\Models\Product::where('name', 'like', "%{$this->stockReplaceSearch}%")
                                 ->orWhere('sku',  'like', "%{$this->stockReplaceSearch}%")
                                 ->orderBy('name')
                                 ->limit(20)
                                 ->get()
            : collect();

        $layout = auth()->user()?->isAdmin() ? 'layouts.admin' : 'layouts.staff';
        return view('livewire.admin.order', compact('orders', 'statusCounts', 'pendingReceiptCount', 'stockReplaceProducts'))->layout($layout);
    }
}
