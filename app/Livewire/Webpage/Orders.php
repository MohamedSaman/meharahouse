<?php
namespace App\Livewire\Webpage;
use Livewire\Component;
class Orders extends Component {
    public function render() {
        return view('livewire.webpage.orders')->layout('layouts.webpage')->title('My Orders — Meharahouse');
    }
}
