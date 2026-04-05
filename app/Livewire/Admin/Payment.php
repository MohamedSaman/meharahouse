<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

#[Title('Payments')]
#[Layout('layouts.admin')]
class Payment extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $filterMethod = '';

    public function render()
    {
        $payments = Order::with('user')
            ->when($this->search, fn($q) => $q->where('order_number', 'like', "%{$this->search}%")
                ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$this->search}%")))
            ->when($this->filterStatus, fn($q) => $q->where('payment_status', $this->filterStatus))
            ->when($this->filterMethod, fn($q) => $q->where('payment_method', $this->filterMethod))
            ->latest()
            ->paginate(20);

        $summary = [
            'total_paid'    => Order::where('payment_status', 'paid')->sum('total'),
            'total_pending' => Order::where('payment_status', 'pending')->sum('total'),
            'total_failed'  => Order::where('payment_status', 'failed')->sum('total'),
            'total_refunded'=> Order::where('payment_status', 'refunded')->sum('total'),
        ];

        return view('livewire.admin.payment', compact('payments', 'summary'));
    }
}
