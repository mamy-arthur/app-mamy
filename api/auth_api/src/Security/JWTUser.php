<?php


namespace App\Security;


use Common\DTO\PlatformUserDto;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;

class JWTUser extends PlatformUserDto implements JWTUserInterface
{

    /**
     * @inheritDoc
     */
    public static function createFromPayload($username, array $payload)
    {
        return new PlatformUserDto($payload);
    }

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
