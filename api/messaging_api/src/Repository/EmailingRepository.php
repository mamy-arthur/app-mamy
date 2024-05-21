<?php

namespace App\Repository;

use App\Entity\Emailing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Emailing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Emailing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Emailing[]    findAll()
 * @method Emailing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Emailing::class);
    }

    // /**
    //  * @return Emailing[] Returns an array of Emailing objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Emailing
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
