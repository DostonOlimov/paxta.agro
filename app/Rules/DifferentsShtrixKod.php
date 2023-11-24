<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DifferentsShtrixKod implements Rule
{
    public function passes($attribute, $value)
    {
        $fromNumber = request()->input('from_kod');
        $toNumber = request()->input('to_kod');

        return ($toNumber - $fromNumber) < 2000;
    }

    public function message()
    {
        return 'Jami olingan na\'munalar soni 2000 dan kam bo\'lishi kerak.';
    }
}
