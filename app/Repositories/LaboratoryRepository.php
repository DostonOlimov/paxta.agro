<?php


namespace App\Repositories;

use App\Models\Application;
use App\Models\Laboratories;

class LaboratoryRepository
{
    public function create(array $data): Laboratories
    {
        return Laboratories::create($data);
    }

    public function findLaboratoryByStateId($stateId): Laboratories
    {
        return Laboratories::whereHas('city', function ($query) use ($stateId) {
            $query->where('state_id', $stateId);
        })->first();

    }
}
