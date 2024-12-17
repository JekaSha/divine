<?php


namespace App\Services;

use App\Repositories\UserRepository;
use App\Services\PermissionService;

class UserService
{
    protected $userRepository;
    protected $userId;
    protected $permissionService;

    public function __construct(UserRepository $userRepository, PermissionService $permissionService)
    {
        $this->userRepository = $userRepository;
        $this->permissionService = $permissionService;
    }

    /**
     *
     * @param int $userId
     * @param string $name
     * @param mixed $value
     * @param bool $force
     * @return void
     */
    public function setProp(string $name, $value, int $userId = null, bool $force = false)
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

    public function setUserId(int $userId) {
        if ($userId) {
            $this->userId = $userId;
            $this->userRepository->setUserId($userId);
            $this->permissionService->setUserId($userId);
            return true;
        } else {
            return false;
        }
    }
    public function getUserId() {
        return $this->userId;
    }

    public function handlePackage(array $package): void
    {
        $type = $package['type'] ?? null;

        switch ($type) {
            case 'requests_per_month':
                $this->permissionService->extendPackage($package['days']);
                $this->permissionService->addRequests($package['requests']);
                break;

            case 'feature_access':
                $this->permissionService->setPermission('feature_' . $package['feature'], true);
                break;

            case 'custom_property':
                $this->setProp($package['key'], $package['value'], $this->userId);
                break;

            default:
                throw new \InvalidArgumentException("Unknown package type: {$type}");
        }
    }

    public function tm($name = "expired_date") {
        return $this->permissionService->tm($name);
    }

}
