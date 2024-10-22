<?php

namespace App\Application\QueryHandler;

use App\Application\Query\FindCarsUnfitForRoadQuery;
use App\Domain\Car\CarRepositoryInterface;

class FindCarsUnfitForRoadHandler {
    private CarRepositoryInterface $carRepository;

    public function __construct(CarRepositoryInterface $carRepository) {
        $this->carRepository = $carRepository;
    }

    public function __invoke(FindCarsUnfitForRoadQuery $query): array {
        try {
            return $this->carRepository->findCarsUnfitForRoad();
        } catch (\Exception $e) {
            throw new \RuntimeException('An error occurred while finding unfit cars for road: ' . $e->getMessage());
        }
    }
}
