<?php
namespace App\Repositories;

use App\Models\Challenge;
use Illuminate\Database\Eloquent\Collection;

class ChallengeRepository implements ChallengeRepositoryInterface
{
    public function getAll(): Collection
    {
        return Challenge::all();
    }

    public function getById(int $id)
    {
        return Challenge::find($id);
    }

    public function create(array $data)
    {
        return Challenge::create($data);
    }

    public function update(int $id, array $data)
    {
        $challenge = $this->getById($id);

        if (!$challenge) {
            return null;
        }

        $challenge->update($data);
        return $challenge;
    }

    public function delete(int $id)
    {
        $challenge = $this->getById($id);

        if ($challenge) {
            $challenge->delete();
            return true;
        }

        return false;
    }
}
