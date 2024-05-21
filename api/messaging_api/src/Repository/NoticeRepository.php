<?php

namespace App\Repository;

use App\Entity\Notice;
use Common\DTO\ListFetchingParamsDto;
use Common\Helper\DBItemsListHelper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class NoticeRepository extends ServiceEntityRepository
{
    protected string $tableName = Notice::TABLE_NAME;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notice::class);
    }

    private function getNoticesListQuery(
        ?int $items = 2,
        ?int $offset = 0
    ): string {
        $sql = "
            SELECT DISTINCT msg.id id, msg.message AS message, msg.type AS type, msg.start_date start_date, msg.end_date end_date
            FROM $this->tableName msg
            order by msg.id		           
		";

        $sql .= $items ? " OFFSET $offset LIMIT $items" : '';

        return $sql;
    }

    private function getCorrespondences(string $key = null)
    {
        $attributes = [
            'id' => 'notices.id',
            'message' => 'notices.message',
            'start_date' => 'notices.start_date',
            'end_date' => 'notices.end_date',
            'created_at' => 'notices.created_at',
            'updated_at' => 'notices.updated_at',
            'type' => 'notices.type'
        ];

        return $key ? $attributes[$key] : $attributes;
    }

    private function getListQuery(
        array $filters = [],
        array $orders = [],
        ?int $items = 10,
        ?int $offset = 0
    ) {
        $attributes = (function (array $array) {
            foreach ($array as $key => $val) {
                yield "$val AS $key";
            }
        })($this->getCorrespondences());

        $attributes = implode(',', iterator_to_array($attributes));

        $sql = "SELECT
		$attributes
		FROM messaging_api__notices notices
		ORDER BY notices.updated_at DESC ";

        return $sql;
    }

    /**
     * @param int $items
     * @param int $offset
     * @return array|null
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function findAllNotices(int $items = 2, int $offset = 0): ?array
    {
        $sql = $this->getNoticesListQuery($items, $offset);
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param string $noticeType
     * @return array|null
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function findNoticesByType(string $noticeType): ?array
    {
        $sql = "
            SELECT msg.id id, msg.message AS message, msg.type AS type, msg.start_date start_date, msg.end_date end_date
            FROM $this->tableName msg
            WHERE msg.type = '$noticeType'
              AND msg.start_date::timestamp <= now() 
              AND now() <= msg.end_date::timestamp
            order by msg.id		           
		";
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getNoticesListing(
        ListFetchingParamsDto $listParameters
    ): ?array {
        return DBItemsListHelper::getWholeResulForQuery(
            $this->getEntityManager()->getConnection(),
            $this->getListQuery(),
            $listParameters
        );
    }
}
