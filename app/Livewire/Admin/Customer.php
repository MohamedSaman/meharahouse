<?php
namespace App\Livewire\Admin;
use Livewire\Component;
use Livewire\Attributes\Title;

class Customer extends Component {
    public function render() {
        return view('livewire.admin.customer')->layout('layouts.admin')->layoutData(['pageTitle' => 'Customers', 'pageSubtitle' => 'Manage customer accounts']);
    }
}
