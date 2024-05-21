<?php

namespace Common\Traits;

use Doctrine\ORM\Mapping as ORM;

trait TimestampableEntityTrait
{

    /**
     * @ORM\Column(type="datetimetz_immutable", nullable=true)
     */
    protected ?\DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetimetz_immutable", nullable=true)
     */
    protected ?\DateTimeImmutable $updatedAt;

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
