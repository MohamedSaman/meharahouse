<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class PaymentReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order, public float $amount) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Received — ' . $this->order->order_number);
    }

    public function headers(): Headers
    {
        return new Headers(
            text: [
                'X-Mailer'   => 'Meharahouse Mailer',
                'X-Priority' => '3',
            ],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment-received');
    }
}
