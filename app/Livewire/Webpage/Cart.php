<?php
namespace App\Livewire\Webpage;
use Livewire\Component;
class Cart extends Component {
    public function render() {
        return view('livewire.webpage.cart')->layout('layouts.webpage')->title('Cart — Meharahouse');
    }
}
