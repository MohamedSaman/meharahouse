<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // ── PayHere ──────────────────────────────────────────────────────────

    public function payhereForm(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('payment_status', 'pending')
            ->firstOrFail();

        return view('payment.payhere', compact('order'));
    }

    public function payhereProcess(Request $request, string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('payment_status', 'pending')
            ->firstOrFail();

        // Dummy: simulate successful payment
        $order->update([
            'payment_status' => 'paid',
            'status'         => 'payment_received',
        ]);

        return redirect()->route('payment.success', $orderNumber);
    }

    // ── PayPal ───────────────────────────────────────────────────────────

    public function paypalForm(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('payment_status', 'pending')
            ->firstOrFail();

        return view('payment.paypal', compact('order'));
    }

    public function paypalProcess(Request $request, string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('payment_status', 'pending')
            ->firstOrFail();

        // Dummy: simulate successful payment
        $order->update([
            'payment_status' => 'paid',
            'status'         => 'payment_received',
        ]);

        return redirect()->route('payment.success', $orderNumber);
    }

    // ── Success ──────────────────────────────────────────────────────────

    public function success(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        return view('payment.success', compact('order'));
    }
}
