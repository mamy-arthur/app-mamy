<?php

namespace Common\Http;
// todo: move to Helper/Http

use Common\DTO\ListFetchingFilterDTO;
use Common\DTO\ListFetchingParamsDto;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use function Symfony\Component\String\u;

class RequestHelper
{
    const INTERNAL_API_BASE_URLS = [
        '/auth-api' => 'http://nginx_app:4041',
        '/users-api' => 'http://nginx_app:4045',
        '/messaging-api' => 'http://nginx_app:4043',
        '/storage-api' => 'http://nginx_app:4044',
    ];

    static function getListFetchingParameters(
        Request $request,
        ListFetchingParamsDto $defaults = null
    ): ListFetchingParamsDto {
        $fetchingParams = $defaults ?? new ListFetchingParamsDto();

        $fetchingParams->page = $request->query->get('page') ?? $defaults->page;
        $fetchingParams->items =
            $request->query->get('items') ?? $defaults->items;

        $orderByString = $request->query->get('order_by');
        $orderByValuesString = $request->query->get('order_by_values');

        $format = $request->query->get('format') ?? 'camelcase';

        if ($defaults) {
            $fetchingParams->orderBy = $defaults->orderBy;
            $fetchingParams->filters = $defaults->filters;
        }

        if ($orderByString && $orderByValuesString) {
            $orderKeys = array_map(function (string $key) use ($format) {
                return $format == 'camelcase' ? u($key)->camel() : $key;
            }, str_getcsv($orderByString));

            $orderValues = str_getcsv($orderByValuesString);

            $fetchingParams->orderBy = array_combine($orderKeys, $orderValues);
        }

        $filtersString = $request->query->get('filter_by');
        $filtersValuesString = $request->query->get('filter_by_values');

        if ($filtersString && $filtersValuesString) {
            $filtersKeys = array_map(function (string $key) use ($format) {
                return $format == 'camelcase' ? u($key)->camel() : $key;
            }, str_getcsv($filtersString));

            $filtersValues = str_getcsv($filtersValuesString);

            $fetchingParams->filters = array_combine(
                $filtersKeys,
                $filtersValues,
            );
        }

        return $fetchingParams;
    }

    static function getListingFetchingParameters(
        Request $request,
        ListFetchingParamsDto $defaults = null
    ): ListFetchingParamsDto {
        $fetchingParams =
            $defaults ?? new ListFetchingParamsDto(['items' => -1]);

        $fetchingParams->page = $request->query->get(
            'page',
            $fetchingParams->page,
        );
        $fetchingParams->items = $request->query->get(
            'items',
            $fetchingParams->items,
        );
        $fetchingParams->search = $request->query->get(
            'search',
            $fetchingParams->search,
        );

        $reqOrderBy = (array) ($request->query->get('order_by') ?? []);
        if (count($reqOrderBy) > 0) {
            $fetchingParams->orderBy = []; // we remove any default order by
        }
        foreach ($reqOrderBy as $order) {
            /**
             * @var string $part_0
             * @var string $part_1
             */
            extract(explode(',', $order), EXTR_PREFIX_ALL, 'part');

            if ($part_0) {
                $direction = strtoupper(
                    !isset($part_1) || !$part_1 ? 'ASC' : $part_1,
                );
                if (!in_array($direction, ['ASC', 'DESC'])) {
                    throw new BadRequestHttpException(
                        "The provided order direction '$part_1' is unknown.",
                    );
                }
                $fetchingParams->orderBy[$part_0] = $direction;
            } else {
                throw new BadRequestHttpException(
                    'Not enough data were provided to process the requested orders.',
                );
            }

            unset($part_0, $part_1);
        }

        $reqFilterBy = (array) ($request->query->get('filter_by') ?? []);
        foreach ($reqFilterBy as $filter) {
            $filterParts = explode(',', $filter);

            if (count($filterParts) < 3 && $filterParts[0]) {
                throw new BadRequestHttpException(
                    "The url filter '$filterParts[0]' has not been provided with enough data.",
                );
            }

            try {
                $filterBy = new ListFetchingFilterDTO([
                    'type' => $filterParts[1],
                    'values' => array_splice($filterParts, 2),
                ]);
            } catch (Exception $exception) {
                throw new BadRequestHttpException(
                    'Something went wrong when processing the provided filters.',
                );
            }

            $fetchingParams->filters[$filterParts[0]] = $filterBy;

            unset($filterParts, $filterBy);
        }

        return $fetchingParams;
    }

    static function getQueryParamsString(ListFetchingParamsDto $params)
    {
        $queryParams = [];

        if ($params->page) {
            array_push($queryParams, "page=$params->page");
        }
        if ($params->items) {
            array_push($queryParams, "items=$params->items");
        }
        if ($params->search) {
            array_push($queryParams, "search=$params->search");
        }
        if (count($params->filters)) {
            /**
             * @var string $field
             * @var ListFetchingFilterDTO $data
             */
            foreach ($params->filters as $field => $data) {
                $values = implode(',', $data->values);
                array_push(
                    $queryParams,
                    "filter_by[]=$field,$data->type,$values",
                );
            }
        }
        if (count($params->orderBy)) {
            foreach ($params->orderBy as $field => $direction) {
                array_push($queryParams, "order_by[]=$field,$direction");
            }
        }

        return implode('&', $queryParams);
    }

    static function getUrlResponse(
        string $url,
        array $context = [],
        bool $asJson = true
    ) {
        $url = static::getInternalUrl($url);

        $response = file_get_contents(
            $url,
            false,
            stream_context_create($context),
        );

        if ($asJson) {
            $response = json_decode($response);
        }

        return $response;
    }

    static function getInternalUrl(string $url)
    {
        $output = $url;

        foreach (static::INTERNAL_API_BASE_URLS as $base => $internal) {
            if (strpos($url, $base) === 0) {
                $output = str_replace($base, $internal, $url);

                break;
            }
        }

        return $output;
    }

    static function getHeaders(Request $request, array $headers): array
    {
        $output = !count($headers) ? $request->headers->all() : [];

        foreach ($headers as $headerKey => $header) {
            if (is_string($headerKey)) {
                $output[$headerKey] = $request->headers->get($header);
            } else {
                $output[] = $request->headers->get($header);
            }
        }

        return $output;
    }
}
