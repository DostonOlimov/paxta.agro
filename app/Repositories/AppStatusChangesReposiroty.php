<?php

namespace App\Repositories;

use App\Models\AppStatusChanges;
use App\Models\Contracts\TestProgramInterface;
use App\Models\TestPrograms;
use App\Repositories\Contracts\AppStatusChangesRepositoryInterface;

class AppStatusChangesReposiroty implements AppStatusChangesRepositoryInterface
{
    protected string $model = AppStatusChanges::class;

    public function create(array $data): AppStatusChanges
    {
        return $this->model::create($data);
    }
}
