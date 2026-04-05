<?php

namespace App\Livewire\Webpage;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.webpage.index')
            ->layout('layouts.webpage')
            ->title('Meharahouse — Quality You Can Trust');
    }
}
