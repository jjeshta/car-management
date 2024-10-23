<?php

namespace App\Tests\Application\CommandHandler;

use App\Application\Command\RemoveCarCommand;
use App\Application\CommandHandler\RemoveCarHandler;
use App\Application\Service\LoggerService;
use App\Domain\Car\CarRepositoryInterface;
use App\Domain\Car\Car;
use PHPUnit\Framework\TestCase;

class RemoveCarHandlerTest extends TestCase
{
    private $carRepository;
    private $loggerService;
    private $removeCarHandler;

    protected function setUp(): void
    {
        // Mock dependencies
        $this->carRepository = $this->createMock(CarRepositoryInterface::class);
        $this->loggerService = $this->createMock(LoggerService::class);

        // Create the RemoveCarHandler with mocked dependencies
        $this->removeCarHandler = new RemoveCarHandler(
            $this->carRepository,
            $this->loggerService
        );
    }

    public function testHandleSuccessfullyRemovesCar(): void
    {
        $registrationNumber = '1234 AB 12';
        $car = $this->createMock(Car::class);
        $command = new RemoveCarCommand($registrationNumber);

        $matcher = $this->exactly(2);
        $this->loggerService
            ->expects($matcher)
            ->method('logInfo')
            ->willReturnCallback(function ($message) use ($matcher, $registrationNumber) {
                match ($matcher->numberOfInvocations()) {
                    1 => $this->assertEquals("Attempting to remove car with registration number: {$registrationNumber}", $message),
                    2 => $this->assertEquals("Car with registration number: {$registrationNumber} has been removed successfully.", $message),
                };
            });

        $this->carRepository->expects($this->once())
            ->method('findByRegistrationNumber')
            ->with($registrationNumber)
            ->willReturn($car);

        $this->carRepository->expects($this->once())
            ->method('remove')
            ->with($car);

        $this->removeCarHandler->handle($command);

        $this->assertTrue(true);
    }

    public function testHandleCarNotFound(): void
    {
        $registrationNumber = 'XYZ987';
        $command = new RemoveCarCommand($registrationNumber);

        $this->loggerService
            ->expects($this->once())
            ->method('logInfo')
            ->with("Attempting to remove car with registration number: {$registrationNumber}");

        $matcher = $this->exactly(2);
        $this->loggerService
            ->expects($matcher)
            ->method('logError')
            ->willReturnCallback(function ($message) use ($matcher, $registrationNumber) {
                match ($matcher->numberOfInvocations()) {
                    1 => $this->assertEquals("Car not found with registration number: {$registrationNumber}", $message),
                    2 => $this->assertEquals("An error occurred while removing the car: Car not found", $message),
                    default => $this->fail('Unexpected invocation of logError')
                };
            });

        $this->carRepository->expects($this->once())
            ->method('findByRegistrationNumber')
            ->with($registrationNumber)
            ->willReturn(null);

        $this->carRepository->expects($this->never())
            ->method('remove');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('An error occurred: Car not found');

        $this->removeCarHandler->handle($command);
    }



    public function testHandleThrowsRuntimeExceptionOnError(): void
    {
        $registrationNumber = '1234 WW 12';
        $command = new RemoveCarCommand($registrationNumber);

        $this->loggerService->expects($this->once())
            ->method('logInfo')
            ->with("Attempting to remove car with registration number: {$registrationNumber}");

        $this->carRepository->expects($this->once())
            ->method('findByRegistrationNumber')
            ->with($registrationNumber)
            ->willThrowException(new \RuntimeException('Database error'));

        $this->loggerService->expects($this->once())
            ->method('logError')
            ->with('An error occurred while removing the car: Database error');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('An error occurred: Database error');

        $this->removeCarHandler->handle($command);
    }
}
