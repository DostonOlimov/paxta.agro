<?php

namespace App\Repositories;

use App\Models\Application;
use App\Models\Contracts\ApplicationInterface;
use App\Repositories\Contracts\ApplicationRepositoryInterface;

class ApplicationRepository implements ApplicationRepositoryInterface
{
    protected string $model = Application::class;

    public function create(array $data): ApplicationInterface
    {
        return $this->model::create($data);
    }

    public function update(int $id, array $data): ?ApplicationInterface
    {
        $app = $this->model::find($id);
        if (!$app) {
            return null;
        }
        $app->update($data);
        return $app;
    }

    public function find(int $id): ?ApplicationInterface
    {
        return $this->model::find($id);
    }

    public function delete(int $id): bool
    {
        $app = $this->model::findOrFail($id);
        return $app->delete();
    }
}
