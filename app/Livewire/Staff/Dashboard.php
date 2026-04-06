<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Order;
use App\Models\OrderReturn;
use Illuminate\Support\Facades\DB;

#[Title('Staff Dashboard')]
#[Layout('layouts.staff')]
class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            'new_orders'        => Order::whereIn('status', ['new', 'payment_received'])->count(),
            'processing'        => Order::whereIn('status', ['confirmed', 'sourcing'])->count(),
            'dispatched'        => Order::where('status', 'dispatched')->count(),
            'completed_today'   => Order::whereIn('status', ['delivered', 'completed'])
                                        ->whereDate('updated_at', today())->count(),
            'open_returns'      => OrderReturn::whereIn('status', ['requested', 'pickup_arranged', 'received'])->count(),
            'pending_payments'  => Order::where('payment_status', 'partial')
                                        ->whereNotIn('status', ['cancelled'])->count(),
        ];

        $recentOrders = Order::with('user')
            ->latest()
            ->limit(8)
            ->get();

        $pendingPaymentOrders = Order::where('payment_status', 'partial')
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->latest()
            ->limit(5)
            ->get();

        return view('livewire.staff.dashboard', compact('stats', 'recentOrders', 'pendingPaymentOrders'));
    }
}
