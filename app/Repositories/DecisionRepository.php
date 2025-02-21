<?php

namespace App\Repositories;

use App\Models\Decision;

class DecisionRepository
{
    public function create(array $data): Decision
    {
        return Decision::create($data);
    }
}
