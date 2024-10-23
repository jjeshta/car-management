<?php

namespace App\Application\QueryHandler;

use App\Application\Query\FindCarQuery;
use App\Application\Service\LoggerService;
use App\Domain\Car\CarRepositoryInterface;

class FindCarHandler
{
    public function __construct(
        private CarRepositoryInterface $carRepository,
        private LoggerService $loggerService
    ) {}

    public function handle(FindCarQuery $query)
    {
        $this->loggerService->logInfo("Attempting to find car with registration number: {$query->getRegistrationNumber()}");

        try {
            $car = $this->carRepository->findByRegistrationNumber($query->getRegistrationNumber());
            if (!$car) {
                $this->loggerService->logError("Car not found with registration number: {$query->getRegistrationNumber()}");
                throw new \Exception('Car not found.');
            }
            $this->loggerService->logInfo("Car found successfully: {$car->getRegistrationNumber()}");

            return $car;
        } catch (\Exception $e) {
            $this->loggerService->logError('An error occurred while finding the car: ' . $e->getMessage());

            throw new \RuntimeException('An error occurred while finding the car: ' . $e->getMessage());
        }
    }
}
