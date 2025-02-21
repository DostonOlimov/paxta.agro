<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FilterRow extends Component
{
    public array $filters;
    public array $filterValues;

    /**
     * Create a new component instance.
     *
     * @param array $filters
     * @param array $filterValues
     */
    public function __construct(array $filters, array $filterValues = [])
    {
        $this->filters = $filters;
        $this->filterValues = $filterValues;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.filter-row');
    }
}
