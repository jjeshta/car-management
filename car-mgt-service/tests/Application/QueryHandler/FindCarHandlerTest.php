<?php

namespace App\Tests\Application\QueryHandler;

use App\Application\Query\FindCarQuery;
use App\Application\QueryHandler\FindCarHandler;
use App\Application\Service\LoggerService;
use App\Domain\Car\Car;
use App\Domain\Car\CarRepositoryInterface;
use PHPUnit\Framework\TestCase;

class FindCarHandlerTest extends TestCase
{
    private $carRepository;
    private $loggerService;
    private $findCarHandler;

    protected function setUp(): void
    {
        $this->carRepository = $this->createMock(CarRepositoryInterface::class);
        $this->loggerService = $this->createMock(LoggerService::class);

        $this->findCarHandler = new FindCarHandler(
            $this->carRepository,
            $this->loggerService
        );
    }

    public function testHandleSuccessfullyFindsCar(): void
    {
        $registrationNumber = '1234 AB 56';
        $car = $this->createMock(Car::class);
        $car->expects($this->once())
            ->method('getRegistrationNumber')
            ->willReturn($registrationNumber);

        $query = new FindCarQuery($registrationNumber);

        $this->loggerService->expects($this->exactly(2))
            ->method('logInfo')
            ->willReturnCallback(function ($message) use ($registrationNumber) {
                static $call = 0;
                $call++;
                if ($call === 1) {
                    $this->assertEquals("Attempting to find car with registration number: {$registrationNumber}", $message);
                } elseif ($call === 2) {
                    $this->assertEquals("Car found successfully: {$registrationNumber}", $message);
                }
            });

        $this->carRepository->expects($this->once())
            ->method('findByRegistrationNumber')
            ->with($registrationNumber)
            ->willReturn($car);

        $result = $this->findCarHandler->handle($query);

        $this->assertSame($car, $result);
    }

    public function testHandleCarNotFoundThrowsException(): void
    {
        $registrationNumber = 'XYZ987';
        $query = new FindCarQuery($registrationNumber);

        $this->loggerService->expects($this->exactly(2))
            ->method('logError')
            ->willReturnCallback(function ($message) use ($registrationNumber) {
                static $call = 0;
                $call++;
                if ($call === 1) {
                    $this->assertEquals("Car not found with registration number: {$registrationNumber}", $message);
                } elseif ($call === 2) {
                    $this->assertEquals("An error occurred while finding the car: Car not found.", $message);
                }
            });

        $this->carRepository->expects($this->once())
            ->method('findByRegistrationNumber')
            ->with($registrationNumber)
            ->willReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('An error occurred while finding the car: Car not found.');

        // Act
        $this->findCarHandler->handle($query);
    }

    public function testHandleThrowsRuntimeExceptionOnError(): void
    {
        $registrationNumber = 'DEF456';
        $query = new FindCarQuery($registrationNumber);
    
        $this->loggerService->expects($this->once()) 
            ->method('logError')
            ->willReturnCallback(function ($message) {
                $this->assertEquals("An error occurred while finding the car: Database error", $message);
            });
    
        $this->carRepository->expects($this->once())
            ->method('findByRegistrationNumber')
            ->with($registrationNumber)
            ->willThrowException(new \RuntimeException('Database error'));
    
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('An error occurred while finding the car: Database error');
    
        $this->findCarHandler->handle($query);
    }
}
