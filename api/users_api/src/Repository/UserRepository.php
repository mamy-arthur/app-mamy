<?php
namespace App\Repository;

use App\Entity\User;
use Common\Helper\DBItemsListHelper;
use Common\DTO\ListFetchingParamsDto;
use Common\Traits\RepositoryListingTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{

	use RepositoryListingTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

	private function getCorrespondences(string $key = null) {
		
		$attributes = [
			"id" => "users.id",
			"first_name" => "users.first_name",
			"last_name" => "users.last_name",
			"email" => "users.email",
			"mobile_number" => "users.mobile_number",
			"address" => "users.address",
			"registration_number" => "users.registration_number",
			"is_active" => "users.is_active",
			"name" => "services.name",
			"code" => "services.code",
		];

		return $key ? $attributes[$key] : $attributes;

	}

	private function getListQuery(array $filters = [], array $orders = [], ?int $items = 10, ?int $offset = 0) {

		$attributes = (function(array $array) {
			foreach ($array as $key => $val) { yield "$val AS $key"; }
		})($this->getCorrespondences());

		$attributes = implode(",", iterator_to_array($attributes));

		$sql = "SELECT
		$attributes
		FROM users_api__users users, users_api__services services
		WHERE users.service_id = services.id
		ORDER BY users.id DESC ";
		
		return $sql;

	}

	public function getUsersListing(ListFetchingParamsDto $listParameters): ?array {
		return DBItemsListHelper::getWholeResulForQuery(
			$this->getEntityManager()->getConnection(),
			$this->getListQuery(),
			$listParameters
		);
	}

	public function getUsersListingCount(ListFetchingParamsDto $listParameters): int {
		return DBItemsListHelper::getTotalCountForQuery(
			$this->getEntityManager()->getConnection(),
			$this->getListQuery(),
			$listParameters,
			[],
			'id'
		);
	}
}
