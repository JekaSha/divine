<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;

interface ChallengeRepositoryInterface
{
    /**
     * Get challenges by identifier and optional user ID.
     *
     * @param string $identifier
     * @param int|null $userId
     * @return Collection
     */
    public function get(string $identifier, array $filters = []): Collection;

    /**
     * Store a new challenge.
     *
     * @param string $identifier
     * @param array $challengeData
     * @return mixed
     */
    public function store(string $identifier, array $challengeData);

    /**
     * Get a challenge by ID.
     *
     * @param int $id
     * @return mixed
     */
    public function getById(int $id);

    /**
     * Create a new challenge.
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Update an existing challenge.
     *
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function update(int $id, array $data);

    /**
     * Delete a challenge by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
