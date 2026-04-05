<?php
namespace App\Livewire\Webpage;
use Livewire\Component;
class Shop extends Component {
    public function render() {
        return view('livewire.webpage.shop')->layout('layouts.webpage')->title('Shop — Meharahouse');
    }
}
