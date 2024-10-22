<?php

namespace App\Domain\ServiceHistory;

use App\Domain\Car\Car;

interface ServiceHistoryRepositoryInterface {
    public function save(ServiceHistory $serviceHistory): void;
    public function remove(ServiceHistory $serviceHistory): void;
    public function findByCar(Car $car): array;
    public function find(int $id): ?ServiceHistory;
}
