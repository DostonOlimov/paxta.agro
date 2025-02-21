<?php

namespace App\Repositories;

use App\Models\CropData;

class CropDataRepository
{
    public function create(array $data): CropData
    {
        return CropData::create($data);
    }
}
