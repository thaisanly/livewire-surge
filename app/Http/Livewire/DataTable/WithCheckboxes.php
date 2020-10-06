<?php

namespace App\Http\Livewire\DataTable;

trait WithCheckboxes
{
    public $checkedPage = false;
    public $checkedAll = false;
    public $checked = [];

    protected function initializeWithCheckBoxes()
    {
        $this->beforeRender(function () {
            $this->checkIfCheckedAll();
        });
    }

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

    public function getCheckedRowsQueryProperty()
    {
        return $this->checkedAll
            ? (clone $this->rowsQuery)
            : (clone $this->rowsQuery->whereKey($this->checked));
    }
}
