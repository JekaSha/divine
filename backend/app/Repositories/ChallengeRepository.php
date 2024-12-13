<?php

namespace App\Repositories;

use App\Models\Challenge;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\ChallengeRepositoryInterface;
use Illuminate\Support\Str;

class ChallengeRepository implements ChallengeRepositoryInterface
{
    /**
     * Get challenges by identifier and optional user ID.
     *
     * @param string $identifier
     * @param int|null $userId
     * @return Collection
     */
    public function get(string $identifier, array $filters = []): Collection
    {
        $query = Challenge::query();

        if (isset($filters['user_id'])) {
            $query->orWhere('user_id', $filters['user_id']);
        }

        if (isset($filters['session_hash'])) {
            $query->where('session_hash', $filters['session_hash']);
        }

        $query->orWhere('guest_hash', $identifier);

        if (isset($filters['sort_by'])) {
            $direction = $filters['sort_order'] ?? 'asc';
            $query->orderBy($filters['sort_by'], $direction);
        }

        return $query->get();
    }


    /**
     * Store a new challenge.
     *
     * @param string $identifier
     * @param array $challengeData
     * @return Challenge
     */
    public function store(string $identifier, array $challengeData): Challenge
    {
        $data = [
            'guest_hash'    => $challengeData['guest_hash'] ?? $identifier,
            'user_id'       => $challengeData['user_id'] ?? null,
            'session_hash'  => $challengeData['session_hash'] ?? null,
            'prompt_id'     => $challengeData['prompt_id'] ?? null,
            'request'       => $challengeData['request'],
            'prompt'        => $challengeData['prompt'] ?? '',
            'response'      => $challengeData['response'] ?? null,
            'response_time' => $challengeData['response_time'] ?? null,
            'stream'        => $challengeData['stream'] ?? null,
        ];


        $existing = Challenge::where('session_hash', $data['session_hash'])
            ->where('request', $data['request'])
            ->first();

        if ($existing) {
            return $existing;
        }

        return Challenge::create($data);
    }


    /**
     * Get a challenge by ID.
     *
     * @param int $id
     * @return Challenge|null
     */
    public function getById(int $id): ?Challenge
    {
        return Challenge::find($id);
    }

    /**
     * Create a new challenge.
     *
     * @param array $data
     * @return Challenge
     */
    public function create(array $data): Challenge
    {
        return Challenge::create($data);
    }

    /**
     * Update an existing challenge.
     *
     * @param int $id
     * @param array $data
     * @return Challenge|null
     */
    public function update(int $id, array $data): ?Challenge
    {
        $challenge = $this->getById($id);

        if ($challenge) {
            $challenge->update($data);
        }

        return $challenge;
    }

    /**
     * Delete a challenge by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $challenge = $this->getById($id);

        if ($challenge) {
            $challenge->delete();
            return true;
        }

        return false;
    }
}
