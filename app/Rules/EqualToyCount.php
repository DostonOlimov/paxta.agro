<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class EqualToyCount implements Rule
{
    public function passes($attribute, $value)
    {
        $fromNumber = request()->input('from_kod');
        $toNumber = request()->input('to_kod');
        $toy_count = request()->input('toy_count');
        $from_toy = request()->input('from_toy');
        $to_toy = request()->input('to_toy');

        return ($toNumber - $fromNumber+1) == $toy_count and ($to_toy - $from_toy+1) == $toy_count;
    }

    public function message()
    {
        return 'Jami olingan na\'munalar soni shtrix kod va toylar ketma-ketligi farqiga teng bo\'lishi kerak.';
    }
}
