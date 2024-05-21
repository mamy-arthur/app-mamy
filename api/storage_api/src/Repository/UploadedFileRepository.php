<?php

namespace App\Repository;

use App\Entity\UploadedFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UploadedFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UploadedFile::class);
    }

    /**
     * @param $fileId int | string
     */
    public function findOne($fileId) {
        if (is_int($fileId)) {
            $output = $this->findOneById($fileId);
        } else {
            $output = $this->findOneByFileName($fileId);
        }

        return $output;
    }
}
