<?php

namespace App\DataFixtures\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class FixtureLog
 * @package App\DataFixtures\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="users_api__fixtures")
 * @ORM\HasLifecycleCallbacks()
 */
class FixtureLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public ?int $id;

    /**
     * An explicit identifier of the fixture (idealy its class name)
     * @ORM\Column(type="string", unique=true)
     *
     * @var string
     */
    public string $fixture;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     * @var \DateTimeImmutable
     */
    public \DateTimeImmutable $createdAt;

    /**
     * @ORM\PrePersist()
     */
    public function setCreatedAt(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
