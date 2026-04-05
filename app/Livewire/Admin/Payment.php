<?php
namespace App\Livewire\Admin;
use Livewire\Component;
class Payment extends Component {
    public function render() {
        return view('livewire.admin.payment')->layout('layouts.admin')->layoutData(['pageTitle' => 'Payments', 'pageSubtitle' => 'View payment transactions']);
    }
}
