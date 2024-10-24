<?php

namespace App\Tests\Application\CommandHandler;

use App\Application\Command\RemoveServiceHistoryCommand;
use App\Application\CommandHandler\RemoveServiceHistoryHandler;
use App\Application\Service\LoggerService;
use App\Domain\ServiceHistory\ServiceHistory;
use App\Domain\ServiceHistory\ServiceHistoryRepositoryInterface;
use PHPUnit\Framework\TestCase;

class RemoveServiceHistoryHandlerTest extends TestCase
{
    private $serviceHistoryRepository;
    private $loggerService;
    private $removeServiceHistoryHandler;

    protected function setUp(): void
    {
        $this->serviceHistoryRepository = $this->createMock(ServiceHistoryRepositoryInterface::class);
        $this->loggerService = $this->createMock(LoggerService::class);
        $this->removeServiceHistoryHandler = new RemoveServiceHistoryHandler(
            $this->serviceHistoryRepository,
            $this->loggerService
        );
    }

    public function testHandleSuccessfullyRemovesServiceHistory(): void
    {
        $serviceHistoryId = 1;
        $command = new RemoveServiceHistoryCommand($serviceHistoryId);
        $serviceHistory = $this->createMock(ServiceHistory::class);

        $this->serviceHistoryRepository->expects($this->once())
            ->method('find')
            ->with($serviceHistoryId)
            ->willReturn($serviceHistory);

        $this->serviceHistoryRepository->expects($this->once())
            ->method('remove')
            ->with($serviceHistory);

        $this->loggerService->expects($this->exactly(2))
            ->method('logInfo')
            ->willReturnCallback(function ($message) use ($serviceHistoryId) {
                static $call = 0;
                $call++;
                match ($call) {
                    1 => $this->assertEquals("Attempting to remove service history with ID: {$serviceHistoryId}", $message),
                    2 => $this->assertEquals("Service history with ID: {$serviceHistoryId} has been removed successfully.", $message),
                };
            });

        $this->removeServiceHistoryHandler->handle($command);
    }

    public function testHandleThrowsExceptionWhenServiceHistoryNotFound(): void
    {
        $serviceHistoryId = 999;
        $command = new RemoveServiceHistoryCommand($serviceHistoryId);

        $this->serviceHistoryRepository->expects($this->once())
            ->method('find')
            ->with($serviceHistoryId)
            ->willReturn(null);

        $this->loggerService->expects($this->once())
            ->method('logError')
            ->with("Service history not found with ID: {$serviceHistoryId}");

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Service history not found with ID: {$serviceHistoryId}");

        $this->removeServiceHistoryHandler->handle($command);
    }

    public function testHandleThrowsRuntimeExceptionOnGeneralError(): void
    {
        $serviceHistoryId = 1;
        $command = new RemoveServiceHistoryCommand($serviceHistoryId);
        $serviceHistory = $this->createMock(ServiceHistory::class);

        $this->serviceHistoryRepository->expects($this->once())
            ->method('find')
            ->with($serviceHistoryId)
            ->willReturn($serviceHistory);

        $this->serviceHistoryRepository->expects($this->once())
            ->method('remove')
            ->will($this->throwException(new \Exception('Database error')));

        $this->loggerService->expects($this->once())
            ->method('logError')
            ->with("An error occurred while removing the service history: Database error");

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('An error occurred: service couldnot be removed Database error');

        $this->removeServiceHistoryHandler->handle($command);
    }
}
