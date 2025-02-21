<?php

namespace App\Repositories;

use App\Models\DefaultModels\tbl_activities;

class ActivityRepository
{
    public function logActivity(array $data)
    {
        return tbl_activities::create($data);
    }
}
