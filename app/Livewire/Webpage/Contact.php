<?php
namespace App\Livewire\Webpage;
use Livewire\Component;
class Contact extends Component {
    public function render() {
        return view('livewire.webpage.contact')->layout('layouts.webpage')->title('Contact — Meharahouse');
    }
}
