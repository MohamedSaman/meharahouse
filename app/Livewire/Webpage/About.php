<?php
namespace App\Livewire\Webpage;
use Livewire\Component;
class About extends Component {
    public function render() {
        return view('livewire.webpage.about')->layout('layouts.webpage')->title('About Us — Meharahouse');
    }
}
