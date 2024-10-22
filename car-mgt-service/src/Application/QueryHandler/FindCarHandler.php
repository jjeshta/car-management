<?php

namespace App\Application\QueryHandler;

use App\Application\Query\FindCarQuery;
use App\Domain\Car\CarRepositoryInterface;

class FindCarHandler {
    public function __construct(private CarRepositoryInterface $carRepository) 
    {}

    public function handle(FindCarQuery $query) {
        try {
            $car = $this->carRepository->findByRegistrationNumber($query->getRegistrationNumber());
            if (!$car) {
                throw new \Exception('Car not found.');
            }

            return $car;
        } catch (\Exception $e) {
            throw new \RuntimeException('An error occurred while finding the car: ' . $e->getMessage());
        }
    }
}