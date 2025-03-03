<?php
namespace App\Repositories\Contracts;

use App\Models\Contracts\ApplicationInterface;

interface ApplicationRepositoryInterface
{
    /**
     * Create a new application
     *
     * @param array $data
     * @return ApplicationInterface
     */
    public function create(array $data): ApplicationInterface;

    /**
     * Update an existing application
     *
     * @param int $id
     * @param array $data
     * @return ApplicationInterface
     */
    public function update(int $id, array $data): ?ApplicationInterface;

    /**
     * Find an application by ID
     *
     * @param int $id
     * @return ApplicationInterface|null
     */
    public function find(int $id): ?ApplicationInterface;

    /**
     * Delete an application by ID
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
