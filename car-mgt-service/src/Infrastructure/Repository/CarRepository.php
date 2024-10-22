<?php

namespace App\Infrastructure\Repository;

use App\Domain\Car\Car;
use App\Domain\Car\CarRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class CarRepository implements CarRepositoryInterface
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {}

    public function save(Car $car): void
    {
        $this->entityManager->persist($car);
        $this->entityManager->flush();
    }

    public function remove(Car $car): void
    {
        $this->entityManager->remove($car);
        $this->entityManager->flush();
    }

    public function findByRegistrationNumber(string $registrationNumber): ?Car
    {
        return $this->entityManager->getRepository(Car::class)
            ->findOneBy(['registrationNumber' => $registrationNumber]);
    }

    public function findCarsFitForRoad(): array
    {
        $cars = $this->entityManager->getRepository(Car::class)->findAll();
        
        return array_filter($cars, fn(Car $car) => $car->isFitForRoad());
    }

    public function findCarsUnfitForRoad(): array
    {
        $cars = $this->entityManager->getRepository(Car::class)->findAll();
        
        return array_filter($cars, fn(Car $car) => !$car->isFitForRoad());
    }
}
