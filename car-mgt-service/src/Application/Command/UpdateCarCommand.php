<?php
namespace App\Application\Command;

class UpdateCarCommand
{
    public function __construct(
        private string $registrationNumber,
        private ?string $make,
        private ?string $model
    ) {}

    public function getRegistrationNumber(): string
    {
        return $this->registrationNumber;
    }

    public function getMake(): ?string
    {
        return $this->make;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }
}
