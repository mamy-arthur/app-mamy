<?php

namespace App\Services;

use App\Entity\Service;
use Doctrine\ORM\EntityManagerInterface;

class ServiceManager
{

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getServices(): array
    {
        return $this->entityManager->getRepository(Service::class)->findAll();
    }

    public function createService($service): void
    {
        $this->entityManager->persist($service);
        $this->entityManager->flush();
    }
}
