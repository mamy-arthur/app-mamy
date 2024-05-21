<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="users_api__users")
 * @UniqueEntity("email")
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public ?int $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    public string $firstName;

    /**
     * @ORM\Column(type="string", length=50)
     */
    public string $lastName;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Service")
     */
    public Service $service;

    /**
     * @ORM\Column(type="string", length=255, unique= true)
     */
    public string $email;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    public ?string $mobileNumber; // todo: rename to phoneNumber

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public ?string $address;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    public ?string $registrationNumber;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    public bool $isActive = true;

    /**
     * @var string[]|null
     */
    public ?array $roles = null;
}
