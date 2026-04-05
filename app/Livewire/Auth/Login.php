<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;

#[Title('Dashboard')]
#[Layout('layouts.auth')]
#[Title('Sign In — Meharahouse')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    protected array $rules = [
        'email'    => 'required|email',
        'password' => 'required|min:6',
    ];

    public function login(): void
    {
        $this->validate();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        session()->regenerate();

        // Role-based redirect
        $user = Auth::user();
        if ($user->isAdmin()) {
            $this->redirect(route('admin.dashboard'), navigate: true);
        } elseif ($user->isStaff()) {
            $this->redirect(route('staff.dashboard'), navigate: true);
        } else {
            $this->redirect(route('webpage.home'), navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
