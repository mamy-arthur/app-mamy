<?php

namespace Common\Helper;

use Common\DTO\ListFetchingFilterDTO;
use Common\DTO\ListFetchingParamsDto;
use Common\Enum\ListFetchingFilterEnum;
use Doctrine\DBAL\Driver\Connection;

class DBItemsListHelper
{
    /**
     * @param string $baseQuery
     * @param string $alias
     * @return string
     */
    static function getWrappedQuery(
        string $baseQuery,
        string $alias = 'base_query'
    ): string {
        return "SELECT * FROM ($baseQuery) $alias";
    }

    /**
     * @param string $baseQuery
     * @param ListFetchingFilterDTO[] $filters
     * @return string
     */
    static function getFiltersAppliedQuery(
        string $baseQuery,
        array $filters = []
    ): string {
        $sql = DBItemsListHelper::getWrappedQuery($baseQuery);

        $isFirst = true;

        foreach ($filters as $field => $filter) {
            if (!$isFirst) {
                $sql .= ' AND (';
            } else {
                $sql .= "\nWHERE (";
            }

            switch ($filter->type) {
                case ListFetchingFilterEnum::EQUALS:
                case ListFetchingFilterEnum::TEXT_EQUALS:
                    $conditions = [];

                    foreach ($filter->values as $test) {
                        if (
                            $filter->type ===
                            ListFetchingFilterEnum::TEXT_EQUALS
                        ) {
                            $conditions[] = "$field::text ~* '^$test$'";
                        } else {
                            $conditions[] = "$field = $test";
                        }
                    }

                    if (count($conditions) > 0) {
                        $sql .= implode(' OR ', $conditions);
                    }

                    unset($conditions);
                    break;

                case ListFetchingFilterEnum::TEXT_INCLUDES_SOME:
                case ListFetchingFilterEnum::TEXT_INCLUDES_SOME_OR_IS_NULL:
                    $conditions = [];

                    if (
                        $filter->type ===
                        ListFetchingFilterEnum::TEXT_INCLUDES_SOME_OR_IS_NULL
                    ) {
                        $conditions[] = "$field IS NULL";
                    }

                    foreach ($filter->values as $test) {
                        $conditions[] = "$field::text ~* '.*$test.*'";
                    }

                    if (count($conditions) > 0) {
                        $sql .= implode(' OR ', $conditions);
                    }

                    unset($conditions);
                    break;

                case ListFetchingFilterEnum::IS_TIME_BETWEEN:
                case ListFetchingFilterEnum::IS_TIME_BETWEEN_OR_IS_NULL:
                    $conditions = [];

                    if (
                        isset($filter->values[0]) &&
                        !in_array($filter->values[0], ['', null])
                    ) {
                        $lowerVal = $filter->values[0];
                        $conditions[] = "$field > '$lowerVal'";
                    }

                    if (
                        isset($filter->values[1]) &&
                        !in_array($filter->values[1], ['', null])
                    ) {
                        $greaterVal = $filter->values[1];
                        $conditions[] = "$field < '$greaterVal'";
                    }

                    if (count($conditions) > 0) {
                        $sql .= implode(' AND ', $conditions);
                    }
                    unset($conditions);
                    break;
            }

            $sql .= ')';
            if ($isFirst) {
                $isFirst = false;
            }
        }

        return $sql;
    }

    /**
     * @param string $baseQuery
     * @param array $orders
     * @return string
     */
    static function getOrderAppliedQuery(
        string $baseQuery,
        array $orders
    ): string {
        $isFirst = true;

        foreach ($orders as $field => $order) {
            if ($isFirst) {
                $baseQuery .= "\nORDER BY ";
            } else {
                $baseQuery .= ', ';
            }

            $baseQuery .= "$field $order";

            if ($isFirst) {
                $isFirst = false;
            }
        }

        return $baseQuery;
    }

    /**
     * @param Connection $conn
     * @param string $sql
     * @param ListFetchingParamsDto $listParameters
     * @param $searchColumns
     * @return array
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    static function getWholeResulForQuery(
        Connection $conn,
        string $sql,
        ListFetchingParamsDto $listParameters,
        $searchColumns = []
    ): array {
        $sql = DBItemsListHelper::getFiltersAppliedQuery(
            $sql,
            $listParameters->filters,
        );
        if ($listParameters->search && count($searchColumns) > 0) {
            $sql = DBItemsListHelper::getSearchAppliedQuery(
                $sql,
                $listParameters->search,
                $searchColumns,
            );
        }
        $sql = DBItemsListHelper::getOrderAppliedQuery(
            $sql,
            $listParameters->orderBy,
        );
        if ((int) $listParameters->items > 0) {
            $sql = DBItemsListHelper::getPaginationAppliedQuery(
                $sql,
                $listParameters->items,
                $listParameters->page,
            );
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param Connection $conn
     * @param string $sql
     * @param ListFetchingParamsDto $listParameters
     * @param $searchColumns
     * @param $unityColumn
     * @return int
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    static function getTotalCountForQuery(
        Connection $conn,
        string $sql,
        ListFetchingParamsDto $listParameters,
        $searchColumns = [],
        $unityColumn = null
    ): int {
        $sql = DBItemsListHelper::getFiltersAppliedQuery(
            $sql,
            $listParameters->filters,
        );
        if ($listParameters->search && count($searchColumns) > 0) {
            $sql = DBItemsListHelper::getSearchAppliedQuery(
                $sql,
                $listParameters->search,
                $searchColumns,
            );
        }

        $countSelect = $unityColumn ?: '*';
        $sql = "SELECT COUNT($countSelect) FROM ($sql) list";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * @param string $baseQuery
     * @param int $items
     * @param int $page
     * @return string
     */
    static function getPaginationAppliedQuery(
        string $baseQuery,
        int $items,
        int $page = 1
    ): string {
        if ($items > 0) {
            $offset = (($page ?: 1) - 1) * $items;

            $baseQuery .= "\nLIMIT $items OFFSET $offset";
        }

        return $baseQuery;
    }

    /**
     * @param string $baseQuery
     * @param string $searchTerm
     * @param array $searchColumns
     * @return string
     */
    static function getSearchAppliedQuery(
        string $baseQuery,
        string $searchTerm,
        array $searchColumns
    ): string {
        $sql = DBItemsListHelper::getWrappedQuery($baseQuery);

        $conditions = [];

        foreach ($searchColumns as $column) {
            $conditions[] = "$column::text ~* '.*$searchTerm.*'";
        }

        $isFirst = true;

        foreach ($conditions as $condition) {
            if ($isFirst) {
                $sql .= "\nWHERE ";
            } else {
                $sql .= ' OR ';
            }

            $sql .= $condition;

            if ($isFirst) {
                $isFirst = false;
            }
        }

        return $sql;
    }
}
