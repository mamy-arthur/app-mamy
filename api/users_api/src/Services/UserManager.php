<?php

namespace App\Services;

use App\Entity\User;
use Common\DTO\ListFetchingParamsDto;
use Common\Entity\AbstractEntityHandler;
use Doctrine\ORM\EntityManagerInterface;

class UserManager extends AbstractEntityHandler
{
    protected AuthManager $authManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        AuthManager $authManager
    ) {
        parent::__construct($entityManager);

        $this->authManager = $authManager;
    }

    public function getUsers(): array
    {
        return $this->getRepository()->findAll();
    }

    public function getUserByUsername(string $username): ?User
    {
        return $this->getRepository()->findOneByEmail($username);
    }

    public function getFullUser(User $user): ?User
    {
        $userRoles = $this->authManager->getRoles($user->email);

        if (isset($userRoles)) {
            $userRoles = array_map(function ($role) {
                return $role->code;
            }, $userRoles);
        }

        $user->roles = $userRoles;

        return $user;
    }

    public function getEntityClassname(): string
    {
        return User::class;
    }

    public function getListing(ListFetchingParamsDto $listParameters): array
    {
        return $this->getRepository()->getUsersListing($listParameters);
    }

    public function getListingCount(ListFetchingParamsDto $listParameters): int
    {
        return $this->getRepository()->getUsersListingCount($listParameters);
    }

    /**
     * @param User $user
     * @param string[] $roles
     */
    public function saveUser(User $user, array $roles)
    {
        $fullRoles = array_filter($this->authManager->getRoles(), function (
            $role
        ) use ($roles) {
            return in_array($role->code, $roles);
        });

        $rolesIds = array_map(function ($role) {
            return $role->id;
        }, $fullRoles);

        $this->authManager->saveCredentials(
            $user->email,
            array_values($rolesIds),
            empty($user->id),
        );

        return parent::save($user);
    }
}
