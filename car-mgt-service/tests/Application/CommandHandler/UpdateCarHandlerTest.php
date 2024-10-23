<?php

namespace App\Tests\Application\CommandHandler;

use App\Application\Command\UpdateCarCommand;
use App\Application\CommandHandler\UpdateCarHandler;
use App\Application\Service\LoggerService;
use App\Domain\Car\CarRepositoryInterface;
use PHPUnit\Framework\TestCase;

class UpdateCarHandlerTest extends TestCase
{
    private CarRepositoryInterface $carRepository;
    private LoggerService $loggerService;
    private UpdateCarHandler $updateCarHandler;

    protected function setUp(): void
    {
        $this->carRepository = $this->createMock(CarRepositoryInterface::class);
        $this->loggerService = $this->createMock(LoggerService::class);
        $this->updateCarHandler = new UpdateCarHandler($this->carRepository, $this->loggerService);
    }

    public function testHandleSuccessfullyUpdatesCar(): void
    {
        $registrationNumber = 'ABC123';
        $make = 'Toyota';
        $model = 'Corolla';
        $command = new UpdateCarCommand($registrationNumber, $make, $model);

        $car = $this->createMock(\stdClass::class);
        $car->method('updateDetails')->with($make, $model, $registrationNumber);

        $this->carRepository
            ->method('findByRegistrationNumber')
            ->with($registrationNumber)
            ->willReturn($car);

        $this->loggerService
            ->expects($this->exactly(2))
            ->method('logInfo')
            ->withConsecutive(
                $this->stringContains("Attempting to update car with registration number: $registrationNumber"),
                $this->stringContains("Car with registration number: $registrationNumber has been updated successfully.")
            );

        $this->carRepository
            ->expects($this->once())
            ->method('save')
            ->with($car);

        $this->updateCarHandler->handle($command);
    }

    public function testHandleThrowsExceptionWhenCarNotFound(): void
    {
        $registrationNumber = 'XYZ789';
        $make = 'Honda';
        $model = 'Civic';
        $command = new UpdateCarCommand($registrationNumber, $make, $model);

        $this->carRepository
            ->method('findByRegistrationNumber')
            ->with($registrationNumber)
            ->willReturn(null);

        $this->loggerService
            ->expects($this->once())
            ->method('logError')
            ->with($this->stringContains("Car not found with registration number: $registrationNumber"));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Car not found');

        $this->updateCarHandler->handle($command);
    }

    public function testHandleLogsErrorAndThrowsRuntimeExceptionOnUnexpectedError(): void
    {
        $registrationNumber = 'ERROR123';
        $make = 'Nissan';
        $model = 'Altima';
        $command = new UpdateCarCommand($registrationNumber, $make, $model);

        $this->carRepository
            ->method('findByRegistrationNumber')
            ->will($this->throwException(new \Exception('Unexpected error')));

        $this->loggerService
            ->expects($this->once())
            ->method('logError')
            ->with($this->stringContains('An error occurred while updating the car: Unexpected error'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('An error occurred: Unexpected error');

        $this->updateCarHandler->handle($command);
    }
}
