<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ErrorNotification extends Component
{
    public string $message;

    public function render()
    {
        return view('livewire.error-notification');
    }
}
