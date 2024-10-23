<?php

namespace App\Tests\Application\CommandHandler;

use App\Application\Command\UpdateCarCommand;
use App\Application\CommandHandler\UpdateCarHandler;
use App\Application\Service\LoggerService;
use App\Domain\Car\CarRepositoryInterface;
use App\Domain\Car\Car;
use PHPUnit\Framework\TestCase;

class UpdateCarHandlerTest extends TestCase
{
    private $carRepository;
    private $loggerService;
    private $updateCarHandler;

    protected function setUp(): void
    {
        $this->carRepository = $this->createMock(CarRepositoryInterface::class);
        $this->loggerService = $this->createMock(LoggerService::class);

        $this->updateCarHandler = new UpdateCarHandler(
            $this->carRepository,
            $this->loggerService
        );
    }

    public function testHandleSuccessfullyUpdatesCar(): void
    {
        $registrationNumber = 'ABC123';
        $make = 'Toyota';
        $model = 'Corolla';
        $car = $this->createMock(Car::class);
        $command = new UpdateCarCommand($registrationNumber, $make, $model);

        $logMatcher = $this->exactly(2);

        $this->loggerService->expects($logMatcher)
            ->method('logInfo')
            ->willReturnCallback(function ($message) use ($logMatcher, $registrationNumber) {
                match ($logMatcher->numberOfInvocations()) {
                    1 => $this->assertEquals("Attempting to update car with registration number: {$registrationNumber}", $message),
                    2 => $this->assertEquals("Car with registration number: {$registrationNumber} has been updated successfully.", $message),
                    default => $this->fail('Unexpected logInfo invocation'),
                };
            });

        $this->carRepository->expects($this->once())
            ->method('findByRegistrationNumber')
            ->with($registrationNumber)
            ->willReturn($car);

        $car->expects($this->once())
            ->method('updateDetails')
            ->with($make, $model, $registrationNumber);

        $this->carRepository->expects($this->once())
            ->method('save')
            ->with($car);

        $this->updateCarHandler->handle($command);

        $this->assertTrue(true);
    }

    public function testHandleCarNotFound(): void
    {
        $registrationNumber = 'XYZ987';
        $command = new UpdateCarCommand($registrationNumber, 'Ford', 'Fiesta');

        $logMatcher = $this->exactly(2);

        $this->loggerService->expects($logMatcher)
            ->method('logError')
            ->willReturnCallback(function ($message) use ($logMatcher, $registrationNumber) {
                match ($logMatcher->numberOfInvocations()) {
                    1 => $this->assertEquals("Car not found with registration number: {$registrationNumber}", $message),
                    2 => $this->assertEquals("An error occurred while updating the car: Car not found", $message),
                    default => $this->fail('Unexpected logError invocation')
                };
            });

        $this->loggerService->expects($this->once())
            ->method('logInfo')
            ->with("Attempting to update car with registration number: {$registrationNumber}");

        $this->carRepository->expects($this->once())
            ->method('findByRegistrationNumber')
            ->with($registrationNumber)
            ->willReturn(null);

        $this->carRepository->expects($this->never())
            ->method('save');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('An error occurred: Car not found');

        $this->updateCarHandler->handle($command);
    }


    public function testHandleThrowsRuntimeExceptionOnError(): void
    {
        $registrationNumber = 'DEF456';
        $make = 'BMW';
        $model = 'X5';
        $command = new UpdateCarCommand($registrationNumber, $make, $model);

        $this->loggerService->expects($this->once())
            ->method('logInfo')
            ->with("Attempting to update car with registration number: {$registrationNumber}");

        $this->carRepository->expects($this->once())
            ->method('findByRegistrationNumber')
            ->with($registrationNumber)
            ->willThrowException(new \RuntimeException('Database error'));

        $this->loggerService->expects($this->once())
            ->method('logError')
            ->with('An error occurred while updating the car: Database error');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('An error occurred: Database error');

        $this->updateCarHandler->handle($command);
    }
}
