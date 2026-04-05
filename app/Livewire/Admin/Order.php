<?php
namespace App\Livewire\Admin;
use Livewire\Component;
class Order extends Component {
    public function render() {
        return view('livewire.admin.order')->layout('layouts.admin')->layoutData(['pageTitle' => 'Orders', 'pageSubtitle' => 'Manage all customer orders']);
    }
}
