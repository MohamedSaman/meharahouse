<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class Profile extends Component
{
    public function render()
    {
        $user = auth()->user();
        $layout = ($user && $user->isAdmin()) ? 'layouts.admin' : 'layouts.staff';

        return view('livewire.admin.profile')->layout($layout);
    }
}
