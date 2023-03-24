<?php

namespace App\Http\Livewire;

use Livewire\Component;

class SuccessNotification extends Component
{
    public $message;

    public function render()
    {
        return view('livewire.success-notification');
    }
}
