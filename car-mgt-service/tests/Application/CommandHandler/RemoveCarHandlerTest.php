<?php

namespace App\Tests\Application\CommandHandler;

use App\Application\Command\RemoveCarCommand;
use App\Application\CommandHandler\RemoveCarHandler;
use App\Application\Service\LoggerService;
use App\Domain\Car\CarRepositoryInterface;
use PHPUnit\Framework\TestCase;

class RemoveCarHandlerTest extends TestCase
{
    private CarRepositoryInterface $carRepository;
    private LoggerService $loggerService;
    private RemoveCarHandler $removeCarHandler;

    protected function setUp(): void
    {
        $this->carRepository = $this->createMock(CarRepositoryInterface::class);
        $this->loggerService = $this->createMock(LoggerService::class);
        $this->removeCarHandler = new RemoveCarHandler($this->carRepository, $this->loggerService);
    }

    public function testHandleSuccessfullyRemovesCar(): void
    {
        $registrationNumber = 'ABC123';
        $command = new RemoveCarCommand($registrationNumber);

        $this->carRepository
            ->method('findByRegistrationNumber')
            ->with($registrationNumber)
            ->willReturn(new \stdClass());

        $this->loggerService
            ->expects($this->exactly(2))
            ->method('logInfo')
            ->withConsecutive(
                $this->stringContains("Attempting to remove car with registration number: $registrationNumber"),
                $this->stringContains("Car with registration number: $registrationNumber has been removed successfully.")
            );

        $this->carRepository
            ->expects($this->once())
            ->method('remove');

        $this->removeCarHandler->handle($command);
    }

    public function testHandleThrowsExceptionWhenCarNotFound(): void
    {
        $registrationNumber = 'XYZ789';
        $command = new RemoveCarCommand($registrationNumber);

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

        $this->removeCarHandler->handle($command);
    }

    public function testHandleLogsErrorAndThrowsRuntimeExceptionOnUnexpectedError(): void
    {
        $registrationNumber = 'ERROR123';
        $command = new RemoveCarCommand($registrationNumber);

        $this->carRepository
            ->method('findByRegistrationNumber')
            ->will($this->throwException(new \Exception('Unexpected error')));

        $this->loggerService
            ->expects($this->once())
            ->method('logError')
            ->with($this->stringContains('An error occurred while removing the car: Unexpected error'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('An error occurred: Unexpected error');

        $this->removeCarHandler->handle($command);
    }
}
