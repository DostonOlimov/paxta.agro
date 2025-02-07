<?php

namespace App\HelperClasses;

use App\Models\ChigitTips;

class ChigitQualityEvaluator
{
    protected $application;
    protected $values = [];
    protected $tip = null;
    protected $quality = false;

    public function __construct($application)
    {
        $this->application = $application;
        $this->fetchChigitValues();
        $this->determineTip();
        $this->evaluateQuality();
    }

    private function fetchChigitValues()
    {
        $indicators = [
            'nuqsondorlik' => 9,
            'tukdorlik' => 12,
            'namlik' => 11,
            'zararkunanda' => 10
        ];

        foreach ($indicators as $key => $id) {
            $this->values[$key] = optional(
                $this->application->chigit_result()->where('indicator_id', $id)->first()
            )->value;
        }
    }

    private function determineTip()
    {
        if (!$this->values['nuqsondorlik'] || !$this->values['tukdorlik']) {
            return;
        }

        $tipQuery = ChigitTips::where('nuqsondorlik', '>=', $this->values['nuqsondorlik']);

        if ($this->application->crops->name_id == 2) {
            $tipQuery->whereBetween('tukdorlik', [$this->values['tukdorlik'], $this->values['tukdorlik']]);
        }

        $this->tip = $tipQuery
            ->where('crop_id', $this->application->crops->name_id)
            ->first();
    }

    private function evaluateQuality()
    {
        if (!$this->tip) {
            return;
        }

        $this->quality =
            $this->values['namlik'] <= $this->tip->namlik &&
            $this->values['tukdorlik'] <= $this->tip->tukdorlik &&
            $this->values['tukdorlik'] >= $this->tip->tukdorlik_min;
    }

    public function getResults()
    {
        return [
            'nuqsondorlik' => $this->values['nuqsondorlik'],
            'tukdorlik' => $this->values['tukdorlik'],
            'namlik' => $this->values['namlik'],
            'zararkunanda' => $this->values['zararkunanda'],
            'tip' => $this->tip,
            'quality' => $this->quality
        ];
    }
}

