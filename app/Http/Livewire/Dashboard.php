<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithCheckboxes;

class Dashboard extends Component
{
    use WithPagination, WithCheckboxes, WithSorting;

    public $search = '';
    public $showCreateModal = false;
    public $showDeleteModal = false;
    public $showEditModal = false;

    public $showFilters = false;

    public $filters = [
        'status' => '',
        'amount-min' => null,
        'amount-max' => null,
        'date-before' => null,
        'date-after' => null,
    ];

    public Transaction $creating;
    public Transaction $editing;
    public $selectedBulkAction = '';

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

    public function updated($field)
    {
        if (in_array($field, ['search', 'sortField', 'sortDirection', 'filters', 'page'])) {
            Cache::forget($this->id);
        }
    }

    public function create()
    {
        $this->editing = new Transaction;
        $this->editing->created_at = now();
        $this->editing->status = 'processing';

        $this->showEditModal = true;
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

        Cache::forget($this->id);
    }

    public function exportChecked()
    {
        $transactions = $this->checkedRowsQuery;

        return response()->streamDownload(fn() => print($transactions->toCsv()), 'transactions.csv');
    }

    public function confirmDelete()
    {
        $this->showDeleteModal = true;
    }

    public function deleteChecked()
    {
        $transactions = $this->checkedRowsQuery;

        $count = $transactions->count();

        $transactions->delete();

        $this->showDeleteModal = false;

        $this->dispatchBrowserEvent('notify', "Successfully deleted {$count} ".Str::plural('transaction', $count));

        Cache::forget($this->id);
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function applyFilters($query)
    {
        return $query
            ->when($this->filters['status'], fn($query, $status) => $query->whereStatus($status))
            ->when($this->filters['amount-min'], fn($query, $amount) => $query->where('amount', '>=', $amount))
            ->when($this->filters['amount-max'], fn($query, $amount) => $query->where('amount', '<=', $amount))
            ->when($this->filters['date-after'], fn($query, $date) => $query->where('created_at', '>=', Carbon::parse($date)))
            ->when($this->filters['date-before'], fn($query, $date) => $query->where('created_at', '<=', Carbon::parse($date)));
    }

    public function getRowsQueryProperty()
    {
        return $this->applyFilters(
            Transaction::search('title', $this->search)
        )
            ->orderBy($this->sortField, $this->sortDirection)
            ->latest();
    }

    public function getRowsProperty()
    {
        return Cache::remember($this->id, now()->addMinutes(10), function () {
            return $this->rowsQuery->paginate(10);
        });
    }

    /** Overriding this method to add cache-busing. */
    public function setPage($page)
    {
        $this->page = $page;

        Cache::forget($this->id);
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'transactions' => $this->rows,
        ]);
    }
}
