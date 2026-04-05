<?php
namespace App\Livewire\Admin;
use Livewire\Component;
class Product extends Component {
    public function render() {
        return view('livewire.admin.product')->layout('layouts.admin')->layoutData(['pageTitle' => 'Products', 'pageSubtitle' => 'Manage your product catalog']);
    }
}
