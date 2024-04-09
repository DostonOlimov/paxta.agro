<?php

namespace App\Rules;

use App\Models\InXaus;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ExsistInXaus implements Rule
{
    public $state_id;

    public function __construct($state_id)
    {
        $this->state_id = $state_id;
    }

    public function passes($attribute, $value)
    {
        $date =  join('-', array_reverse(explode('-', request()->input('date'))));
        $in_xaus = InXaus::whereDate('date','<=',$date)
            ->where('state_id',$this->state_id)
            ->orderBy('date', 'desc')
            ->first();

        return ! is_null($in_xaus);
    }

    public function message()
    {
        return 'Ushbu kiritilgan sanada In Xaus ma\'lumotlari kiritilmagan.';
    }
}
