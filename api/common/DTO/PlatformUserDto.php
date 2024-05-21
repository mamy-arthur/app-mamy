<?php

namespace Common\DTO;

use Symfony\Component\Security\Core\User\UserInterface;

class PlatformUserDto extends UserProfileDto implements UserInterface
{
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->username = $data['username'];
        $this->roles = $data['roles'];
    }

    public string $username;

    public array $roles;

    /**
     * @inheritDoc
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @inheritDoc
     */
    public function getPassword(): ?string
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }
}
