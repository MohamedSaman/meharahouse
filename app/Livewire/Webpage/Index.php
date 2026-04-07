<?php

namespace App\Livewire\Webpage;

use Livewire\Component;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Mail\NewsletterWelcome;
use Illuminate\Support\Facades\Mail;

class Index extends Component
{
    public string $subscribeEmail = '';
    public bool   $subscribed     = false;
    public string $subscribeError = '';

    public function subscribe(): void
    {
        $this->subscribeError = '';
        $this->validate(['subscribeEmail' => 'required|email|max:255']);

        if (\App\Models\NewsletterSubscriber::where('email', $this->subscribeEmail)->exists()) {
            $this->subscribeError = 'You are already subscribed!';
            return;
        }

        $email = $this->subscribeEmail;

        \App\Models\NewsletterSubscriber::create([
            'email'  => $email,
            'source' => 'website',
        ]);

        try { Mail::to($email)->send(new NewsletterWelcome($email)); } catch (\Throwable) {}

        $this->subscribeEmail = '';
        $this->subscribed     = true;
    }

    public function render()
    {
        $banners = Banner::active()->take(5)->get();

        $categories = Category::active()
            ->root()
            ->withCount(['products' => fn($q) => $q->active()])
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        $featuredProducts = Product::active()
            ->featured()
            ->inStock()
            ->with('category')
            ->take(8)
            ->latest()
            ->get();

        $newArrivals = Product::active()
            ->inStock()
            ->with('category')
            ->latest()
            ->take(8)
            ->get();

        return view('livewire.webpage.index', compact('banners', 'categories', 'featuredProducts', 'newArrivals'))
            ->layout('layouts.webpage')
            ->title('Meharahouse — Quality You Can Trust');
    }
}
