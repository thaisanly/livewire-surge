<?php

namespace App\Http\Livewire\DataTable;

use Illuminate\Support\Facades\Cache;

trait WithSorting
{
    public $sortField;
    public $sortDirection = 'asc';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;

        Cache::forget($this->id);
    }
}
