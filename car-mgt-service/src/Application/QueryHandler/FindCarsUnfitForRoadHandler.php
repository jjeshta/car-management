<?php

namespace App\Application\QueryHandler;

use App\Application\Query\FindCarsUnfitForRoadQuery;
use App\Domain\Car\CarRepositoryInterface;
use App\Application\Service\LoggerService;

class FindCarsUnfitForRoadHandler
{

    public function __construct(
        private CarRepositoryInterface $carRepository,
        private LoggerService $loggerService
    ) {}

    public function handle(FindCarsUnfitForRoadQuery $query): array
    {

        $this->loggerService->logInfo("Attempting to find cars unfit for road.");

        try {
            $unfitCars = $this->carRepository->findCarsUnfitForRoad();
            $this->loggerService->logInfo("Successfully retrieved " . count($unfitCars) . " cars unfit for road.");

            return $unfitCars;
        } catch (\Exception $e) {
            $this->loggerService->logError('An error occurred while finding unfit cars for road: ' . $e->getMessage());
            throw new \RuntimeException('An error occurred while finding unfit cars for road: ' . $e->getMessage());
        }
    }
}
