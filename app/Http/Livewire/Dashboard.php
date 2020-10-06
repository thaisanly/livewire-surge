<?php

namespace App\Http\Livewire;

use App\Http\Livewire\DataTable\WithCheckboxes;
use Livewire\Component;
use App\Models\Transaction;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination, WithCheckboxes;

    public $search = '';
    public $sortField;
    public $sortDirection = 'asc';
    public $showEditModal = false;
    public $selectedBulkAction = '';
    public Transaction $editing;

    protected $queryString = [
        'sortField',
        'sortDirection' => ['except' => 'asc'],
    ];

    protected $rules = [
        'editing.title' => 'required',
        'editing.status' => 'required',
        'editing.amount' => 'required',
        'editing.date' => 'required',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function edit(Transaction $transaction)
    {
        // Only reset the currently editing model if it's different. This
        // preserves any un-saved changes when re-opening the modal.
        if (! isset($this->editing) || $transaction->isNot($this->editing)) {
            $this->editing = $transaction;
        }

        $this->showEditModal = true;
    }

    public function save()
    {
        $this->validate();

        $this->editing->save();

        $this->showEditModal = false;
    }

    public function getRowsProperty()
    {
        return Transaction::query()
            ->when($this->search, function ($query, $search) {
                return $query->where('title', 'like', "%{$search}%");
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->latest()
            ->paginate(10);
    }

    public function render()
    {
        $this->checkIfCheckedAll();

        return view('livewire.dashboard', [
            'transactions' => $this->rows,
        ]);
    }
}
