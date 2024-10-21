<?php

namespace App\Domain\Car;

interface CarRepositoryInterface {
    public function save(Car $car): void;
    public function remove(Car $car): void;
    public function findByRegistrationNumber(string $registrationNumber): ?Car;
}
