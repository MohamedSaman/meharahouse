<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class WelcomeUser extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Meharahouse — Your Account is Ready! 🎉');
    }

    public function headers(): Headers
    {
        return new Headers(
            messageId: 'welcome-' . $this->user->id . '@mehrahouse.com',
            references: [],
            text: [
                'X-Mailer'           => 'Meharahouse Mailer',
                'X-Priority'         => '3',
                'Precedence'         => 'bulk',
            ],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.welcome-user');
    }
}
