<?php

namespace Common\DTO;

class ListFetchingParamsDto
{
    public function __construct(array $data = [])
    {
        $data['items'] = $data['items'] ?? $this->items;

        if ($data) {
            $this->page = $data['page'] ?? $this->page;
            $this->items = $data['items'] == -1 ? null : $data['items'];
            $this->format = $data['format'] ?? $this->format;
            $this->filters = $data['filters'] ?? $this->filters;
            $this->orderBy = $data['orderBy'] ?? $this->orderBy;
            $this->search = $data['search'] ?? $this->search;
        }
    }

    public int $page = 1;
    public ?int $items = 10;
    public ?string $format = null;
    public array $filters = [];
    public array $orderBy = [];
    public string $search = '';
}
