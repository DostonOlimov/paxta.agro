<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DifferentsShtrixKod implements Rule
{
    public function passes($attribute, $value)
    {

        $kod_toy = request()->input('kod_toy');
        if (count($kod_toy[0]) == 4) {
            $number = $this->between_count($kod_toy, 0, 1);
        }
        else{
            $number = $this->between_count($kod_toy, 1, 2);
        }
        
        return $number < 4000;
    }

    private function between_count($array, $from, $to)
    {
        $add = 0;

        for ($i = 0; $i < count($array); $i++) {
            $add += $array[$i][$to] - $array[$i][$from];
        }
        return $add;
    }
    public function message()
    {
        return 'Jami olingan na\'munalar soni 2000 dan kam bo\'lishi kerak.';
    }
}
