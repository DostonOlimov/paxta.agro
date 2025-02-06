<?php
namespace App\Services;

class SortService
{
    protected $sortBy;
    protected $sortOrder;
    protected $routeName;

    public function __construct($routeName)
    {
        $this->sortBy = request()->get('sort_by');
        $this->sortOrder = request()->get('sort_order', 'asc');
        $this->routeName = $routeName;
    }

    public function sortable($column, $label)
    {
        $newOrder = ($this->sortBy === $column && $this->sortOrder === 'asc') ? 'desc' : 'asc';
        $icon = ($this->sortBy === $column) ? ($this->sortOrder === 'asc' ? 'fa-arrow-up' : 'fa-arrow-down') : '';

        return '<a href="' . route($this->routeName, ['sort_by' => $column, 'sort_order' => $newOrder]) . '">'
            . trans($label)
            . ($icon ? ' <i class="fa ' . $icon . '"></i>' : '')
            . '</a>';
    }
}
