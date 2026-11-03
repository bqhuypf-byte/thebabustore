<?php

namespace App\Livewire;

use App\Models\Transaction;
use Livewire\Component;

class TransactionsList extends Component
{
    public string $search = '';

    public function render()
    {

        return view('livewire.transactions-list', compact('transactions'));
    }
}
