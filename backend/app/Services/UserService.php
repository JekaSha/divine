<?php


namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    protected $userRepository;
    protected $userId;

    public function __construct(int $userId = null, UserRepository $userRepository)
    {
        $this->userId = $userId;
        $this->userRepository = $userRepository;
    }

    /**
     *
     * @param int $userId
     * @param string $name
     * @param mixed $value
     * @param bool $force
     * @return void
     */
    public function setProp(string $name, $value, int $userId = null, bool $force = false): void
    {
        $prop = $this->userRepository->setProp($name, $value, $userId, $force);
        return $prop;
    }

    public function getProp(string $name, int $userId = null) {
        $prop = $this->userRepository->getProp($name, $userId);
        return $prop;
    }

    /**
     *
     * @param array $filters
     * @return mixed
     */
    public function get(array $filters = [])
    {
        return $this->userRepository->get($filters);
    }
}
