<?php

namespace App\Tests\Application\CommandHandler;

use App\Application\Command\AddServiceHistoryCommand;
use App\Application\CommandHandler\AddServiceHistoryHandler;
use App\Application\Service\LoggerService;
use App\Domain\Car\Car;
use App\Domain\ServiceHistory\ServiceHistory;
use App\Domain\ServiceHistory\ServiceHistoryRepositoryInterface;
use App\Domain\Car\CarRepositoryInterface;
use App\Application\DTO\ServiceHistoryDTO;
use PHPUnit\Framework\TestCase;

class AddServiceHistoryHandlerTest extends TestCase
{
    private $serviceHistoryRepository;
    private $carRepository;
    private $loggerService;
    private $addServiceHistoryHandler;

    protected function setUp(): void
    {
        $this->serviceHistoryRepository = $this->createMock(ServiceHistoryRepositoryInterface::class);
        $this->carRepository = $this->createMock(CarRepositoryInterface::class);
        $this->loggerService = $this->createMock(LoggerService::class);

        $this->addServiceHistoryHandler = new AddServiceHistoryHandler(
            $this->serviceHistoryRepository,
            $this->carRepository,
            $this->loggerService
        );
    }

    public function testHandleSuccessfullyAddsServiceHistory(): void
    {
        $registrationNumber = '1234 AB 56';
        $description = 'Service for brakes and oil change';
        $date = '2023-10-15 10:00:00';

        $serviceDTO = new ServiceHistoryDTO($registrationNumber, $date, $description);
        $command = new AddServiceHistoryCommand($serviceDTO);

        $car = $this->createMock(Car::class);
        $this->carRepository->expects($this->once())
            ->method('findByRegistrationNumber')
            ->with($registrationNumber)
            ->willReturn($car);

        $this->serviceHistoryRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(ServiceHistory::class));

        $this->loggerService->expects($this->exactly(3))
            ->method('logInfo')
            ->willReturnCallback(function ($message) use ($registrationNumber) {
                static $call = 0;
                $call++;
                match ($call) {
                    1 => $this->assertEquals("Handling AddServiceHistoryCommand for car registration number: '{$registrationNumber}'", $message),
                    2 => $this->assertEquals("Car with registration number '{$registrationNumber}' found. Proceeding to add service history.", $message),
                    3 => $this->assertEquals("Service history added successfully for car registration number: '{$registrationNumber}'.", $message),
                };
            });

        $this->addServiceHistoryHandler->handle($command);
    }

    public function testHandleThrowsExceptionWhenCarNotFound(): void
    {
        $registrationNumber = '1234 AB 56';
        $description = 'Service for brakes and oil change';
        $date = '2023-10-15 10:00:00';

        $serviceDTO = new ServiceHistoryDTO($registrationNumber, $date, $description);
        $command = new AddServiceHistoryCommand($serviceDTO);

        $this->carRepository->expects($this->once())
            ->method('findByRegistrationNumber')
            ->with($registrationNumber)
            ->willReturn(null);

        $this->loggerService->expects($this->once())
            ->method('logError')
            ->with("Car with registration number '{$registrationNumber}' not found.");

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Car Registration no. not found.');

        $this->addServiceHistoryHandler->handle($command);
    }

    public function testHandleThrowsRuntimeExceptionOnGeneralError(): void
    {
        $registrationNumber = '1234 AB 56';
        $description = 'Service for brakes and oil change';
        $date = '2023-10-15 10:00:00';

        $serviceDTO = new ServiceHistoryDTO($registrationNumber, $date, $description);
        $command = new AddServiceHistoryCommand($serviceDTO);

        $car = $this->createMock(Car::class);

        $this->carRepository->expects($this->once())
            ->method('findByRegistrationNumber')
            ->with($registrationNumber)
            ->willReturn($car);

        $this->serviceHistoryRepository->expects($this->once())
            ->method('save')
            ->will($this->throwException(new \Exception('Database error')));

        $this->loggerService->expects($this->once())
            ->method('logError')
            ->with("Exception thrown while adding service history for car registration number '{$registrationNumber}': Database error");

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('An error occurred: Database error');

        $this->addServiceHistoryHandler->handle($command);
    }
}
