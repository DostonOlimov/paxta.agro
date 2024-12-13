<?php

namespace App\Rules;

use App\Models\Application;
use App\Models\InXaus;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CheckingParyNumber implements Rule
{
    public $factory;
    public $partyNumber;

    public function __construct($id,$party)
    {
        $this->factory = $id;
        $this->partyNumber = $party;
    }

    public function passes($attribute, $value)
    {
        $in_xaus = Application::join('crop_data','applications.crop_data_id','=','crop_data.id')
                        ->where('applications.prepared_id',$this->factory)
                        ->where('crop_data.party_number',$this->partyNumber)
                        ->first();

        return is_null($in_xaus);
    }

    public function message()
    {
        return 'Ushbu partiya raqami olidin kiritilgan.';
    }
}
