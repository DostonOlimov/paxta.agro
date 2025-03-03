<?php
namespace App\Repositories\Contracts;

use App\Models\Contracts\DecisionInterface;

interface DecisionRepositoryInterface
{
    /**
     * Create a new application
     *
     * @param array $data
     * @return DecisionInterface
     */
    public function create(array $data): DecisionInterface;

    /**
     * Update an existing application
     *
     * @param int $id
     * @param array $data
     * @return DecisionInterface
     */
    public function update(int $id, array $data): DecisionInterface;

    /**
     * Find an application by ID
     *
     * @param int $id
     * @return DecisionInterface|null
     */
    public function find(int $id): ?DecisionInterface;

    /**
     * Delete an application by ID
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
