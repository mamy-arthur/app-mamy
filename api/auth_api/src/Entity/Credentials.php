<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use OutOfBoundsException;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="auth_api__credentials")
 * @UniqueEntity("username")
 * @property Role[] $roles
 */
class Credentials implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    public string $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Exclude()
     */
    public ?string $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Exclude()
     */
    public ?string $passwordReset = null;

    /**
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(name="auth_api__users_roles",
     *  joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")},
     *  inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     * @var ArrayCollection|Role[]
     */
    protected Collection $roles;

    /**
     * @var string|null
     */
    public ?string $passwordPlain = null;

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles->toArray();
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
        $this->passwordPlain = null;
        $this->password = null;
    }

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function __set($name, $value)
    {
        switch ($name) {
            case 'roles':
                $this->roles = new ArrayCollection($value ?? []);
                break;

            default:
                throw new OutOfBoundsException(
                    "The property $name is not accessible on this class",
                );
        }
    }

    public function __get($name)
    {
        switch ($name) {
            case 'roles':
                return $this->roles;

            default:
                throw new OutOfBoundsException(
                    "The property $name is not accessible on this class",
                );
        }
    }
}
