<?php

namespace App\Livewire\Inc;

use Livewire\Component;

class Footer extends Component
{
    public function render()
    {
        return view(request()->is('/') ? 'livewire.inc.footer' : 'livewire.inc.store-footer');
    }
}
