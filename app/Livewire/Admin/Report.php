<?php
namespace App\Livewire\Admin;
use Livewire\Component;
class Report extends Component {
    public function render() {
        return view('livewire.admin.report')->layout('layouts.admin')->layoutData(['pageTitle' => 'Reports', 'pageSubtitle' => 'Analytics and business insights']);
    }
}
