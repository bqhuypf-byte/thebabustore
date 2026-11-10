<?php

namespace App\Livewire;

use Livewire\Component;

class Register extends Component
{
    public function mount()
    {
        return redirect()->to('/account/login?tab=register');
    }

    public function render()
    {
        return view('livewire.register');
    }
}

