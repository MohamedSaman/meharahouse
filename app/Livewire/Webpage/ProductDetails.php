<?php
namespace App\Livewire\Webpage;
use Livewire\Component;
class ProductDetails extends Component {
    public function render() {
        return view('livewire.webpage.product-details')->layout('layouts.webpage')->title('Product — Meharahouse');
    }
}
