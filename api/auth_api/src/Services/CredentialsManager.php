<?php

namespace App\Services;

use App\Entity\Credentials;
use Common\Entity\AbstractEntityHandler;
use DateTime;

/**
 * Class CredentialsManager
 * @package App\Services
 */
class CredentialsManager extends AbstractEntityHandler
{
    public function getEntityClassname(): string
    {
        return Credentials::class;
    }

    /**
     * @param Credentials $entity
     */
    public function save($entity)
    {
        if (!$entity->id) {
            $entity->passwordReset = $this->getPasswordResetToken(
                $entity->username,
            );
        }

        parent::save($entity);
    }

    /**
     * @param string $username
     * @return string
     */
    public function getPasswordResetToken(string $username): string
    {
        return md5((new DateTime())->format('U') . $username);
    }
}
