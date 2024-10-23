<?php
namespace App\Application\QueryHandler;

use App\Application\Query\FindCarsFitForRoadQuery;
use App\Domain\Car\CarRepositoryInterface;
use App\Application\Service\LoggerService;

class FindCarsFitForRoadHandler {

    public function __construct(
        private CarRepositoryInterface $carRepository,
        private LoggerService $loggerService
    ) {}

    public function handle(FindCarsFitForRoadQuery $query): array {
        $this->loggerService->logInfo("Attempting to find cars fit for road.");

        try {
            $fitCars = $this->carRepository->findCarsFitForRoad();
            $this->loggerService->logInfo("Successfully retrieved " . count($fitCars) . " cars fit for road.");

            return $fitCars;
        } catch (\Exception $e) {
            $this->loggerService->logError('An error occurred while finding fit cars for road: ' . $e->getMessage());
            throw new \RuntimeException('An error occurred while finding fit cars for road: ' . $e->getMessage());
        }
    }
}
