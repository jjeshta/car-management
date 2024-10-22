<?php
namespace App\Application\QueryHandler;

use App\Application\Query\FindCarsFitForRoadQuery;
use App\Domain\Car\CarRepositoryInterface;

class FindCarsFitForRoadHandler {

    public function __construct(private CarRepositoryInterface $carRepository) {
        $this->carRepository = $carRepository;
    }

    public function handle(FindCarsFitForRoadQuery $query): array {
        try {
            return $this->carRepository->findCarsFitForRoad();
        } catch (\Exception $e) {
            throw new \RuntimeException('An error occurred while finding fit cars for road: ' . $e->getMessage());
        }
    }
}
