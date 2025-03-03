<?php

namespace App\Repositories;

use App\Models\Contracts\DecisionInterface;
use App\Models\Decision;
use App\Repositories\Contracts\DecisionRepositoryInterface;

class DecisionRepository implements DecisionRepositoryInterface
{
    protected string $model = Decision::class;

    public function create(array $data): DecisionInterface
    {
        return $this->model::create($data);
    }

    public function update(int $id, array $data): DecisionInterface
    {
        $app = $this->model::findOrFail($id);
        $app->update($data);
        return $app;
    }

    public function find(int $id): ?DecisionInterface
    {
        $app = $this->model::findOrFail($id);
        return $app;
    }

    public function delete(int $id): bool
    {
        $app = $this->model::findOrFail($id);
        return $app->delete();
    }
}
