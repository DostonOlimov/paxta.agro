<?php

namespace App\Repositories;

use App\Models\Contracts\TestProgramInterface;
use App\Models\TestPrograms;
use App\Repositories\Contracts\TestProgramRepositoryInterface;

class TestProgramRepository implements TestProgramRepositoryInterface
{
    protected string $model = TestPrograms::class;

    public function create(array $data): TestProgramInterface
    {
        return $this->model::create($data);
    }

    public function delete(int $id): bool
    {
        $app = $this->model::findOrFail($id);
        return $app->delete();
    }
}
