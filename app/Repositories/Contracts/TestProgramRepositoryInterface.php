<?php
namespace App\Repositories\Contracts;

use App\Models\Contracts\TestProgramInterface;

interface TestProgramRepositoryInterface
{
    /**
     * Create a new application
     *
     * @param array $data
     * @return TestProgramInterface
     */
    public function create(array $data): TestProgramInterface;

    /**
     * Delete an application by ID
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
