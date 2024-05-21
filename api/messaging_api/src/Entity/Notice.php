<?php

namespace App\Entity;

use App\Repository\NoticeRepository;
use Common\Enum\NoticeTypeEnum;
use Common\Traits\TimestampableEntityTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NoticeRepository::class)
 * @ORM\Table(name=Notice::TABLE_NAME)
 * @ORM\HasLifecycleCallbacks()
 */
class Notice
{
    use TimestampableEntityTrait;

    const TABLE_NAME = 'messaging_api__notices';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    public ?int $id;

    /**
     * @ORM\Column(type="text")
     */
    public string $message;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public \DateTimeImmutable $startDate;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    public \DateTimeImmutable $endDate;

    /**
     * @ORM\Column(type="string", length=130, nullable=true)
     */
    public string $type = NoticeTypeEnum::FLIGHTS_RECAP_HEADER;
}
