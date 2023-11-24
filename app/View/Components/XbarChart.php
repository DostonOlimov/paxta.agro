<?php

namespace App\View\Components;

use Illuminate\View\Component;

class XBarChart extends Component
{
    public $chart_data ;


    public function __construct( $data1)
    {

        $this->chart_data = $data1;
    }


    public function render()
    {
        return view('components.xbar_chart',
            [
                'chart_data' => $this->chart_data,
            ]);
    }
}
