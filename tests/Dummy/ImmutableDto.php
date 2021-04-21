<?php

namespace Anteris\Tests\DataTransferObjectFactory\Dummy;

class ImmutableDto
{
    public function __construct(
        private string $firstName,
        private string $lastName,
        private string $email,
        private ?string $address
    ) {
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }
}
