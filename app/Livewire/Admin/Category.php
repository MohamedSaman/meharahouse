<?php
namespace App\Livewire\Admin;
use Livewire\Component;
class Category extends Component {
    public function render() {
        return view('livewire.admin.category')->layout('layouts.admin')->layoutData(['pageTitle' => 'Categories', 'pageSubtitle' => 'Manage product categories']);
    }
}
