<?php

namespace App\Repositories;

use App\Models\TestPrograms;

class TestProgramRepository
{
    public function create(array $data): TestPrograms
    {
        return TestPrograms::create($data);
    }
}
