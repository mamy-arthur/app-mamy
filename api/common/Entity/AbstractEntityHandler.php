<?php

namespace Common\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;

/**
 * Class AbstractEntityHandler
 * @package Common
 * @template E
 */
abstract class AbstractEntityHandler
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param E $entity
     */
    public function save($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * @param E $entity
     */
    public function delete($entity)
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /**
     * @return \Doctrine\ORM\EntityRepository|\Doctrine\Persistence\ObjectRepository
     */
    public function getRepository()
    {
        return $this->entityManager->getRepository($this->getEntityClassname());
    }

    /**
     * @param array $criteria
     * @param int $page
     * @param int|null $itemsNumber
     * @param array $order
     * @return array
     */
    public function getEntities(
        array $criteria = [],
        int $page = 1,
        int $itemsNumber = null,
        array $order = []
    ): array {
        return $this->getRepository()->findBy(
            $criteria,
            $order,
            $itemsNumber,
            $page && $itemsNumber ? ($page - 1) * $itemsNumber : null,
        );
    }

    public function getEntitiesCount(array $criteria = []): int
    {
        return $this->getRepository()->count($criteria);
    }

    /**
     * @param array $criteria
     * @return object
     */

    public function getEntity(array $criteria = []): ?object
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    abstract public function getEntityClassname(): string;
}
