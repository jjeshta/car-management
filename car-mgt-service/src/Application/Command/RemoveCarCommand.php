<?php

namespace App\Application\Car\Command;

class RemoveCarCommand
{
    public function __construct(
        private readonly string $carRegistrationNumber
    ) {}

    public function getCarRegistrationNumber(): string
    {
        return $this->carRegistrationNumber;
    }
}