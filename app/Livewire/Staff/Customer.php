<?php
namespace App\Livewire\Staff;
use Livewire\Component;
class Customer extends Component {
    public function render() {
        return view('livewire.staff.customer')->layout('layouts.staff')->layoutData(['pageTitle' => 'Customers']);
    }
}
