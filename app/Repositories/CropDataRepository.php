<?php

namespace App\Repositories;

use App\Models\Contracts\CropDataInterface;
use App\Models\CropData;
use App\Repositories\Contracts\CropDataRepositoryInterface;

class CropDataRepository implements CropDataRepositoryInterface
{
    protected string $model = CropData::class;

    public function create(array $data): CropDataInterface
    {
        return $this->model::create($data);
    }

    public function update(int $id, array $data): CropDataInterface
    {
        $app = $this->model::findOrFail($id);
        $app->update($data);
        return $app;
    }

    public function find(int $id): ?CropDataInterface
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
