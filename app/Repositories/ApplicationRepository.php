<?php

namespace App\Repositories;

use App\Models\Application;

class ApplicationRepository
{
    public function create(array $data): Application
    {
        return Application::create($data);
    }
}
