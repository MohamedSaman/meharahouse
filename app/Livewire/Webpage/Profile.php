<?php

namespace App\Livewire\Webpage;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

#[Title('My Profile')]
#[Layout('layouts.webpage')]
class Profile extends Component
{
    public string $name        = '';
    public string $email       = '';
    public string $phone       = '';
    public string $currentPassword  = '';
    public string $newPassword      = '';
    public string $confirmPassword  = '';

    public function mount(): void
    {
        if (!auth()->check()) {
            $this->redirect(route('auth.login'));
            return;
        }

        $user        = auth()->user();
        $this->name  = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
    }

    public function saveProfile(): void
    {
        $user = auth()->user();

        $this->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $user->update([
            'name'  => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
        ]);

        session()->flash('success', 'Profile updated successfully.');
    }

    public function changePassword(): void
    {
        $this->validate([
            'currentPassword' => ['required', 'string'],
            'newPassword'     => ['required', 'string', 'min:8', 'same:confirmPassword'],
            'confirmPassword' => ['required', 'string'],
        ], [
            'newPassword.same' => 'New password and confirm password do not match.',
        ]);

        if (!Hash::check($this->currentPassword, auth()->user()->password)) {
            $this->addError('currentPassword', 'Current password is incorrect.');
            return;
        }

        auth()->user()->update([
            'password' => Hash::make($this->newPassword),
        ]);

        $this->currentPassword = '';
        $this->newPassword     = '';
        $this->confirmPassword = '';

        session()->flash('success', 'Password changed successfully.');
    }

    public function render()
    {
        return view('livewire.webpage.profile');
    }
}
