<?php

namespace App\Entity;

use App\Repository\EmailingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EmailingRepository::class)
 * @ORM\Table(name="messaging_api__emailing")
 */
class Emailing
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    public ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public string $fromUser; // todo: change "fromUser" to "from"

    /**
     * @ORM\Column(type="string", length=255)
     */
    public string $toUser; // todo: change from "toUser" to "to"

    /**
     * @ORM\Column(type="string", length=255)
     */
    public string $subject;

    /**
     * @ORM\Column(type="string", length=1025)
     */
    public string $content;

    public string $file;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }
}
