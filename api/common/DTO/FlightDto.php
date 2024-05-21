<?php


namespace Common\DTO;


use DateTimeImmutable;

class FlightDto extends BaseDTO
{
    public string $flightNumber;

    public ?DateTimeImmutable $scheduledArrivalTime;

    public ?DateTimeImmutable $estimatedArrivalTime = null;

    public ?DateTimeImmutable $actualArrivalTime = null;

    public ?DateTimeImmutable $scheduledDepartureTime;

    public ?DateTimeImmutable $estimatedDepartureTime = null;

    public ?DateTimeImmutable $actualDepartureTime = null;

    public ?DateTimeImmutable $parkingUnavailabilityStart = null;

    public ?DateTimeImmutable $parkingUnavailabilityEnd = null;

    public ?string $arrivalAirportCode = null;

    public ?string $parkingCode = null;

    public ?string $departureAirportCode = null;

    public ?string $status;

    public function getDepartureTime(): ?DateTimeImmutable
    {
        return $this->actualDepartureTime ?? $this->estimatedDepartureTime ?? $this->scheduledDepartureTime;
    }

    public function getArrivalTime(): ?DateTimeImmutable
    {
        return $this->actualArrivalTime ?? $this->estimatedArrivalTime ?? $this->scheduledArrivalTime;
    }

    public function isFutureFlight(): bool
    {
        return $this->getDepartureTime() && $this->getDepartureTime() > new DateTimeImmutable();
    }

    public function isPastFlight(): bool
    {
        return $this->getArrivalTime() && $this->getArrivalTime() < new DateTimeImmutable();
    }

    public function isPendingFlight(): bool
    {
        return $this->getDepartureTime() && $this->getArrivalTime() && !$this->isFutureFlight() && !$this->isPastFlight();
    }
}
