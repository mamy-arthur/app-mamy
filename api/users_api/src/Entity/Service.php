<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity()
 * @ORM\Table(name="users_api__services")
 * @UniqueEntity("code")
 */
class Service
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
    public string $name;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    public string $code;
   
}