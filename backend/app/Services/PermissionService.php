<?php

namespace App\Services;

use App\Repositories\UserRepository;

class PermissionService
{
    protected $userRepository;
    protected $userId;
    protected $permission;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function setUserId($userId) {
        if ($userId) {
            $this->userId = $userId;
            $this->loadPermissions();
        } else {
            bb("Didn't load permission without UserId");
            return false;
        }
    }

    /**
     * Load the user's current permissions.
     */
    protected function loadPermissions(): void
    {
        $this->permission = $this->userRepository->getProp('permission', $this->userId) ?? [
            'expired_date' => time(), // Default expiration date is now
            'requests' => 0,         // Default number of requests is 0
        ];
    }

    /**
     * Save the user's current permissions.
     */
    protected function savePermissions(): void
    {
        $this->userRepository->setProp('permission', $this->permission, $this->userId, true);
    }

    /**
     * Extend the package validity by a given number of days.
     *
     * @param int $days Number of days to extend the package.
     */
    public function extendPackage(int $days): void
    {
        $this->permission['expired_date'] = strtotime(
            "+{$days} days",
            $this->permission['expired_date'] ?? time()
        );
        $this->savePermissions();
    }

    /**
     * Add a specific number of requests to the user's permissions.
     *
     * @param int $requests Number of requests to add.
     */
    public function addRequests(int $requests): void
    {
        $this->permission['requests'] = ($this->permission['requests'] ?? 0) + $requests;
        $this->savePermissions();
    }

    /**
     * Check if the user has an active package.
     *
     * @return bool Returns true if the package is active, false otherwise.
     */
    public function hasActivePackage(): bool
    {
        return ($this->permission['expired_date'] ?? 0) > time();
    }

    /**
     * Check if a specific permission has not expired.
     *
     * @param string $name Permission name to check.
     * @return bool Returns true if the permission is valid, false otherwise.
     */
    public function tm(string $name = "expired_date"): bool
    {
        if (!isset($this->permission[$name])) {
            return false;
        }

        $expirationTime = $this->permission[$name];

        return is_int($expirationTime) && time() < $expirationTime;
    }

    /**
     * Check if a specific permission exists.
     *
     * @param string $name Permission name.
     * @return mixed Returns the permission value or null if it doesn't exist.
     */
    public function is(string $name)
    {
        return $this->permission[$name] ?? null;
    }

    /**
     * Deduct a specific number of requests from the user's permissions.
     *
     * @param int $requests Number of requests to use (default is 1).
     * @return bool Returns true if the requests were deducted successfully, false otherwise.
     */
    public function useRequest(int $requests = 1): bool
    {
        if (($this->permission['requests'] ?? 0) >= $requests) {
            $this->permission['requests'] -= $requests;
            $this->savePermissions();
            return true;
        }

        return false;
    }

    /**
     * Get the user's current permissions.
     *
     * @return array Returns the current permissions.
     */
    public function getPermissions(): array
    {
        return $this->permission;
    }

    /**
     * Set a new value for a specific permission property.
     *
     * @param string $key Permission property name.
     * @param mixed $value Permission property value.
     */
    public function setPermission(string $key, $value): void
    {
        $this->permission[$key] = $value;
        $this->savePermissions();
    }
}
