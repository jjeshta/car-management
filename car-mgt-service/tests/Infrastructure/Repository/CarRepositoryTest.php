<?php

namespace App\Tests\Infrastructure\Repository;

use App\Domain\Car\Car;
use App\Infrastructure\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class CarRepositoryTest extends TestCase
{
    private $entityManager;
    private $repository;
    private $carRepository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->repository = $this->createMock(EntityRepository::class);

        // Set up the CarRepository with the mocked EntityManager
        $this->carRepository = new CarRepository($this->entityManager);
    }

    public function testSaveCar(): void
    {
        $car = $this->createMock(Car::class);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($car);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->carRepository->save($car);
    }

    public function testRemoveCar(): void
    {
        $car = $this->createMock(Car::class);

        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($car);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->carRepository->remove($car);
    }

    public function testFindByRegistrationNumber(): void
    {
        $car = $this->createMock(Car::class);
        $registrationNumber = '1234 AB 56';

        // Mock the repository behavior for findByRegistrationNumber
        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with(Car::class)
            ->willReturn($this->repository);

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(['registrationNumber' => $registrationNumber])
            ->willReturn($car);

        $result = $this->carRepository->findByRegistrationNumber($registrationNumber);

        $this->assertSame($car, $result);
    }

    public function testFindCarsFitForRoad(): void
    {
        $fitCar = $this->createMock(Car::class);
        $unfitCar = $this->createMock(Car::class);

        // Mocking the fit and unfit cars
        $fitCar->method('isFitForRoad')->willReturn(true);
        $unfitCar->method('isFitForRoad')->willReturn(false);

        $cars = [$fitCar, $unfitCar];

        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with(Car::class)
            ->willReturn($this->repository);

        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn($cars);

        $result = $this->carRepository->findCarsFitForRoad();

        $this->assertCount(1, $result);
        $this->assertSame($fitCar, $result[0]);
    }

    public function testFindCarsUnfitForRoad(): void
    {
        $fitCar = $this->createMock(Car::class);
        $unfitCar = $this->createMock(Car::class);
        $unfitCar1 = $this->createMock(Car::class);

        $fitCar->method('isFitForRoad')->willReturn(true);
        $unfitCar->method('isFitForRoad')->willReturn(false);
        $unfitCar1->method('isFitForRoad')->willReturn(false);

        $cars = [$fitCar, $unfitCar, $unfitCar1];

        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with(Car::class)
            ->willReturn($this->repository);

        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn($cars);

        $result = $this->carRepository->findCarsUnfitForRoad();

        $this->assertCount(2, $result);
    }
}
