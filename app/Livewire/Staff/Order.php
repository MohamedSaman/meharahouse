<?php

namespace App\Livewire\Staff;

use Livewire\Component;

class Order extends Component
{
    public function render()
    {
        return view('livewire.staff.order')
            ->layout('layouts.staff')
            ->layoutData([
                'pageTitle' => 'Order Queue',
            ]);
    }
}
