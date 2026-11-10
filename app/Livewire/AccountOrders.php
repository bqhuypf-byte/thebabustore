<?php

namespace App\Livewire;

use Livewire\Component;

class AccountOrders extends Component
{
    public function mount()
    {
        return redirect()->to('/account?tab=orders');
    }

    public function render()
    {
        return view('livewire.account-orders');
    }
}
