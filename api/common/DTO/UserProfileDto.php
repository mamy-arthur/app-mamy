<?php

namespace Common\DTO;

class UserProfileDto
{
    public function __construct(array $data)
    {
        $this->email = $data['email'] ?? null;
        $this->id = $data['id'] ?? null;
        $this->firstName = $data['first_name'] ?? null;
        $this->lastName = $data['last_name'] ?? null;
        $this->serviceCode = !empty($data['service']) ? $data['service']['code'] : null;
        $this->email = $data['email'] ?? null;
        $this->mobileNumber = $data['mobile_number'] ?? null;
        $this->address = $data['address'] ?? null;
        $this->registrationNumber = $data['registration_number'] ?? null;
        $this->isActive = $data['is_active'] ?? null;
    }

    public ?int $id;

    public ?string $firstName;

    public ?string $lastName;

    public ?string $serviceCode;

    public ?string $email;

    public ?string $mobileNumber;

    public ?string $address;

    public ?string $registrationNumber;

    public ?bool $isActive;
}
