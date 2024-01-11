<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class EqualToyCount implements Rule
{
    public function passes($attribute, $value)
    {
        $toy_count = request()->input('toy_count');
        $kod_toy = request()->input('kod_toy');
        $kod_amount = $this->between_count($kod_toy, 0, 1);
        $toy_amount = $this->between_count($kod_toy, 2, 3);
        // dd($kod_amount == $toy_count and $toy_amount == $toy_count);
        return $kod_amount == $toy_count and $toy_amount == $toy_count;

        //$toy_count = request()->input('toy_count');
        // $fromNumber = request()->input('from_kod');
        // $toNumber = request()->input('to_kod');
        // $from_toy = request()->input('from_toy');
        // $to_toy = request()->input('to_toy');

        // return ($toNumber - $fromNumber+1) == $toy_count and ($to_toy - $from_toy+1) == $toy_count;
    }

    private function between_count($array, $from, $to)
    {
        $add = 0;

        for ($i = 0; $i < count($array); $i++) {
            $add += $array[$i][$to] - $array[$i][$from] + 1;
        }
        return $add;
    }

    public function message()
    {
        return 'Jami olingan na\'munalar soni shtrix kod va toylar ketma-ketligi farqiga teng bo\'lishi kerak.';
    }
}
