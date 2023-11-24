<?php

namespace App\View\Components;

use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class Filter extends Component
{
    public $city ;
    public $crop;
    public $from;
    public $till;


    public function __construct( $city,$crop,$from,$till)
    {

        $this->crop = $crop;
        $this->city = $city;
        $this->from = $from;
        $this->till = $till;
    }


    public function render()
    {
        $states = DB::table('tbl_states')->where('country_id', '=', 234)->get()->toArray();
        $crop_names = DB::table('crops_name')->get()->toArray();
        return view('components.filter',
            [
                'city' => $this->city,
                'crop' => $this->crop,
                'from' => $this->from,
                'till' => $this->till,
                'states' => $states,
                'crop_names' => $crop_names
            ]);
    }
}
