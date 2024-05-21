<?php


namespace Common\DTO;


class FolderFlightsDto extends BaseDTO
{
    public string $folderReference;

    /**
     * @var FlightDto[]
     */
    public array $flights = [];
}
