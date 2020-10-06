<?php

namespace App\Http\Livewire;

use App\Models\Transaction;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        return view('livewire.dashboard', [
            'transactions' => Transaction::search('title', $this->search)->paginate(10),
        ]);
    }
}
