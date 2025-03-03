<?php
namespace App\Repositories\Contracts;

use App\Models\Contracts\CropDataInterface;

interface CropDataRepositoryInterface
{
    /**
     * Create a new application
     *
     * @param array $data
     * @return CropDataInterface
     */
    public function create(array $data): CropDataInterface;

    /**
     * Update an existing application
     *
     * @param int $id
     * @param array $data
     * @return CropDataInterface
     */
    public function update(int $id, array $data): CropDataInterface;

    /**
     * Find an application by ID
     *
     * @param int $id
     * @return CropDataInterface|null
     */
    public function find(int $id): ?CropDataInterface;

    /**
     * Delete an application by ID
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
