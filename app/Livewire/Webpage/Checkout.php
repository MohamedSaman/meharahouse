<?php
namespace App\Livewire\Webpage;
use Livewire\Component;
class Checkout extends Component {
    public function render() {
        return view('livewire.webpage.checkout')->layout('layouts.webpage')->title('Checkout — Meharahouse');
    }
}
