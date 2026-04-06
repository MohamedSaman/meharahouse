<?php

namespace App\Services;

use App\Models\Setting;
use Twilio\Rest\Client as TwilioClient;
use Exception;

class WhatsappService
{
    /**
     * Send a WhatsApp message using the configured active provider.
     *
     * @param  string  $to    Phone number with country code e.g. +251911234567
     * @param  string  $body  Message text
     * @return array{success: bool, message: string}
     */
    public static function send(string $to, string $body): array
    {
        $provider = Setting::get('whatsapp_provider', '');

        return match ($provider) {
            'twilio'   => self::sendViaTwilio($to, $body),
            '360dialog' => self::sendVia360Dialog($to, $body),
            'meta'     => self::sendViaMeta($to, $body),
            default    => ['success' => false, 'message' => 'No WhatsApp provider is configured.'],
        };
    }

    // ── Twilio ──────────────────────────────────────────────────────────────

    public static function sendViaTwilio(string $to, string $body): array
    {
        try {
            $sid    = Setting::get('whatsapp_twilio_account_sid', '');
            $token  = Setting::get('whatsapp_twilio_auth_token', '');
            $from   = Setting::get('whatsapp_twilio_from_number', '');

            if (!$sid || !$token || !$from) {
                return ['success' => false, 'message' => 'Twilio credentials are incomplete.'];
            }

            // Ensure whatsapp: prefix
            $fromFormatted = str_starts_with($from, 'whatsapp:') ? $from : 'whatsapp:' . $from;
            $toFormatted   = str_starts_with($to,   'whatsapp:') ? $to   : 'whatsapp:' . $to;

            $client = new TwilioClient($sid, $token);
            $msg = $client->messages->create($toFormatted, [
                'from' => $fromFormatted,
                'body' => $body,
            ]);

            return ['success' => true, 'message' => 'Sent via Twilio. SID: ' . $msg->sid];

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Twilio error: ' . $e->getMessage()];
        }
    }

    // ── 360dialog ───────────────────────────────────────────────────────────

    public static function sendVia360Dialog(string $to, string $body): array
    {
        try {
            $apiKey = Setting::get('whatsapp_dialog360_api_key', '');
            $phone  = Setting::get('whatsapp_dialog360_phone_number', '');

            if (!$apiKey || !$phone) {
                return ['success' => false, 'message' => '360dialog credentials are incomplete.'];
            }

            $to = preg_replace('/[^0-9]/', '', $to); // strip non-digits

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'D360-API-KEY' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://waba.360dialog.io/v1/messages', [
                'messaging_product' => 'whatsapp',
                'to'   => $to,
                'type' => 'text',
                'text' => ['body' => $body],
            ]);

            if ($response->successful()) {
                return ['success' => true, 'message' => 'Sent via 360dialog.'];
            }

            return ['success' => false, 'message' => '360dialog error: ' . $response->body()];

        } catch (Exception $e) {
            return ['success' => false, 'message' => '360dialog error: ' . $e->getMessage()];
        }
    }

    // ── Order message helpers ───────────────────────────────────────────────

    public static function orderPlaced(\App\Models\Order $order): void
    {
        $phone = self::extractPhone($order);
        if (!$phone) return;

        $site  = \App\Models\Setting::get('site_name', 'Meharahouse');
        $items = $order->items->map(fn($i) => "• {$i->product_name} x{$i->quantity}")->implode("\n");

        self::send($phone,
            "🛍️ *Order Placed — {$site}*\n\n"
          . "Hi {$order->shipping_address['full_name']},\n\n"
          . "Thank you for your order! We've received it and will confirm shortly.\n\n"
          . "📦 *Order No:* {$order->order_number}\n"
          . "💰 *Total:* ETB " . number_format($order->total, 2) . "\n"
          . "💳 *Payment:* " . str_replace('_', ' ', ucwords($order->payment_method)) . "\n\n"
          . "*Items:*\n{$items}\n\n"
          . "We'll keep you updated every step of the way. 🙏"
        );
    }

    public static function orderConfirmed(\App\Models\Order $order): void
    {
        $phone = self::extractPhone($order);
        if (!$phone) return;

        $site = \App\Models\Setting::get('site_name', 'Meharahouse');

        self::send($phone,
            "✅ *Order Confirmed — {$site}*\n\n"
          . "Hi {$order->shipping_address['full_name']},\n\n"
          . "Great news! Your order has been confirmed and we are preparing it for dispatch.\n\n"
          . "📦 *Order No:* {$order->order_number}\n"
          . "💰 *Total:* ETB " . number_format($order->total, 2) . "\n\n"
          . "You'll get another message when your order is on the way! 🚚"
        );
    }

    public static function orderDispatched(\App\Models\Order $order): void
    {
        $phone = self::extractPhone($order);
        if (!$phone) return;

        $site = \App\Models\Setting::get('site_name', 'Meharahouse');

        self::send($phone,
            "🚚 *Order Dispatched — {$site}*\n\n"
          . "Hi {$order->shipping_address['full_name']},\n\n"
          . "Your order is on its way to you! 🎉\n\n"
          . "📦 *Order No:* {$order->order_number}\n"
          . "📍 *Deliver to:* {$order->shipping_address['city']}, {$order->shipping_address['region']}\n\n"
          . "Our delivery team will contact you before arrival. Please keep your phone reachable. 📞"
        );
    }

    public static function orderDelivered(\App\Models\Order $order): void
    {
        $phone = self::extractPhone($order);
        if (!$phone) return;

        $site = \App\Models\Setting::get('site_name', 'Meharahouse');

        self::send($phone,
            "📬 *Order Delivered — {$site}*\n\n"
          . "Hi {$order->shipping_address['full_name']},\n\n"
          . "Your order has been delivered! We hope you love your purchase. ❤️\n\n"
          . "📦 *Order No:* {$order->order_number}\n\n"
          . "If you have any issues, please reply to this message or contact us. Thank you for shopping with {$site}! 🙏"
        );
    }

    public static function orderCompleted(\App\Models\Order $order): void
    {
        $phone = self::extractPhone($order);
        if (!$phone) return;

        $site = \App\Models\Setting::get('site_name', 'Meharahouse');

        self::send($phone,
            "🎊 *Order Completed — {$site}*\n\n"
          . "Hi {$order->shipping_address['full_name']},\n\n"
          . "Your order is fully completed and payment received. Thank you! 🌟\n\n"
          . "📦 *Order No:* {$order->order_number}\n\n"
          . "We'd love to have you shop with us again. Visit us at {$site}! 🛍️"
        );
    }

    public static function paymentReceived(\App\Models\Order $order, float $amount): void
    {
        $phone = self::extractPhone($order);
        if (!$phone) return;

        $site = \App\Models\Setting::get('site_name', 'Meharahouse');

        self::send($phone,
            "💰 *Payment Received — {$site}*\n\n"
          . "Hi {$order->shipping_address['full_name']},\n\n"
          . "We've confirmed your payment of ETB " . number_format($amount, 2) . ".\n\n"
          . "📦 *Order No:* {$order->order_number}\n\n"
          . "Your order is now being processed. Thank you! 🙏"
        );
    }

    private static function extractPhone(\App\Models\Order $order): string
    {
        $address = $order->shipping_address ?? [];
        $phone   = trim($address['phone'] ?? '');

        // Strip spaces and dashes, keep + and digits
        $phone = preg_replace('/[\s\-\(\)]/', '', $phone);

        // Must start with + to be a valid international number
        if (!str_starts_with($phone, '+')) {
            return '';
        }

        return $phone;
    }

    // ── Meta Cloud API ──────────────────────────────────────────────────────

    public static function sendViaMeta(string $to, string $body): array
    {
        try {
            $phoneNumberId = Setting::get('whatsapp_meta_phone_number_id', '');
            $accessToken   = Setting::get('whatsapp_meta_access_token', '');

            if (!$phoneNumberId || !$accessToken) {
                return ['success' => false, 'message' => 'Meta credentials are incomplete.'];
            }

            $to = preg_replace('/[^0-9]/', '', $to);

            $response = \Illuminate\Support\Facades\Http::withToken($accessToken)
                ->post("https://graph.facebook.com/v19.0/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to'   => $to,
                    'type' => 'text',
                    'text' => ['body' => $body],
                ]);

            if ($response->successful()) {
                return ['success' => true, 'message' => 'Sent via Meta Cloud API.'];
            }

            return ['success' => false, 'message' => 'Meta error: ' . $response->body()];

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Meta error: ' . $e->getMessage()];
        }
    }
}
