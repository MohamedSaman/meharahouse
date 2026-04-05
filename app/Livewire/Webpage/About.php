<?php
namespace App\Livewire\Webpage;
use Livewire\Component;
use Livewire\Attributes\Title;  
use Livewire\Attributes\Layout;

#[Title('About Us')]
#[Layout('layouts.webpage')]

class About extends Component {
    public function render() {
        return view('livewire.webpage.about');
    }
}
