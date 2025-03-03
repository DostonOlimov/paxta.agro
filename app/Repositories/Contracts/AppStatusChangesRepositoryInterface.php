<?php
namespace App\Repositories\Contracts;

use App\Models\AppStatusChanges;

interface AppStatusChangesRepositoryInterface
{
    /**
     * Create a new application
     *
     * @param array $data
     * @return AppStatusChanges
     */
    public function create(array $data): AppStatusChanges;

}
