<?php

namespace App\Domain\ServiceHistory;

interface ServiceHistoryRepositoryInterface {
    public function save(ServiceHistory $serviceHistory): void;
    public function remove(ServiceHistory $serviceHistory): void;
    public function find(int $id): ?ServiceHistory;
}
