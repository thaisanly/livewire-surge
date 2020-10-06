<?php

namespace App\Http\Livewire\DataTable;

use Livewire\Livewire;


trait WithCheckboxes
{
    public $checkedPage = false;
    public $checkedAll = false;
    public $checked = [];

    public function updatedChecked()
    {
        $this->checkedPage = false;
        $this->checkedAll = false;
    }

    public function updatedCheckedPage($value)
    {
        $this->checkedAll = false;

        if ($value) {
            $this->checked = $this->rows->pluck('id')->map(fn ($id) => (string) $id);
        } else {
            $this->checked = [];
        }
    }

    public function checkAll()
    {
        $this->checkedAll = true;
    }

    public function checkIfCheckedAll()
    {
        if ($this->checkedAll) {
            $this->checked = $this->rows->pluck('id')->map(fn ($id) => (string) $id);
        }
    }
}
