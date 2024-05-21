<?php

namespace App\Services;

use App\Entity\Emailing;
use Doctrine\ORM\EntityManagerInterface;
use Common\Entity\AbstractEntityHandler;

class EmailingManager extends AbstractEntityHandler
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getEntityClassname(): string
    {
        return Emailing::class;
    }
}
