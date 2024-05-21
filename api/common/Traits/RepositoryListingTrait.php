<?php

namespace Common\Traits;

trait RepositoryListingTrait {

	
	abstract function getCorrespondences(string $key = null);
	abstract function getListQuery(array $filters = [], array $orders = [], ?int $items = 10, ?int $offset = 0) :string;

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getListing(array $filters = [], array $orders = [], int $items = 10, int $offset = 0): ?array {
		
		$filters_query = "";
		foreach ($filters as $key => $value) {
			$value = strtolower($value);
			$value = str_ireplace("'", "''", $value);
			$operator = ($key === array_key_first($filters)) ? "WHERE" : "OR";
			$attribute = $key;
			$filters_query .= " $operator LOWER($attribute) ILIKE '%". $value . "%' ";
		}
		
		$sql = $this->getListQuery($filters, $orders, $items, $offset);
		$sql = "SELECT * FROM ($sql) list $filters_query";
		$sql .= ($items && $offset >= 0) ? " LIMIT $items OFFSET $offset" : "";
		$conn = $this->getEntityManager()->getConnection();
		$stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();
        return $result->fetchAllAssociative();
	}

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getListingCount(array $filters = []): ?int
    {

		$filters_query = "";
		foreach ($filters as $key => $value) {
			$value = strtolower($value);
			$value = str_ireplace("'", "''", $value);
			$operator = ($key === array_key_first($filters)) ? "WHERE" : "OR";
			$attribute = $key;
			$filters_query .= " $operator LOWER($attribute) ILIKE '%". $value . "%' ";
		}

		$sql = $this->getListQuery($filters, [], null, null);
		$sql = "SELECT COUNT(*) FROM ($sql) list $filters_query";
		$conn = $this->getEntityManager()->getConnection();
		$stmt = $conn->prepare($sql);
		$result = $stmt->executeQuery();
		return $result->fetchOne();
    }


	/**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getListingV2(array $filters = [], array $orders = [], int $items = 10, int $offset = 0): ?array {

		//var_dump($orders);

		$filters_query = "";
		foreach ($filters as $key => $filter) {

			$attribute = $key;
			$type = $filter->type;
			$values = $filter->values;
			$value = $values[0]; // only one value for now
			
			$value = strtolower($value);
			$value = str_ireplace("'", "''", $value);
			$operator = ($key === array_key_first($filters)) ? "WHERE" : "AND";
			$filters_query .= " $operator LOWER($attribute) ILIKE '%". $value . "%' ";
		}

		$orders_query = "";
		foreach ($orders as $key => $value) {
			$attribute = $key;
			$order = $value;
			$orders_query .= ($key === array_key_first($orders)) ? "ORDER BY " : "";
			$operator = ($key === array_key_last($orders) ? "" : ", ");
			$orders_query .= "$attribute $order$operator";
		}
		
		$sql = $this->getListQuery($filters, $orders, $items, $offset);
		$sql = "SELECT * FROM ($sql) list $filters_query $orders_query";
		$sql .= ($items && $offset >= 0) ? " LIMIT $items OFFSET $offset" : "";
		$conn = $this->getEntityManager()->getConnection();
		$stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery();
        return $result->fetchAllAssociative();
	}


	/**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getListingCountV2(array $filters = []): ?int
    {

		$filters_query = "";
		foreach ($filters as $key => $filter) {

			$attribute = $key;
			$type = $filter->type;
			$values = $filter->values;
			$value = $values[0]; // only one value for now
			
			$value = strtolower($value);
			$value = str_ireplace("'", "''", $value);
			$operator = ($key === array_key_first($filters)) ? "WHERE" : "AND";
			$filters_query .= " $operator LOWER($attribute) ILIKE '%". $value . "%' ";
		}

		$sql = $this->getListQuery($filters, [], null, null);
		$sql = "SELECT COUNT(*) FROM ($sql) list $filters_query";
		$conn = $this->getEntityManager()->getConnection();
		$stmt = $conn->prepare($sql);
		$result = $stmt->executeQuery();
		return $result->fetchOne();
    }


}

