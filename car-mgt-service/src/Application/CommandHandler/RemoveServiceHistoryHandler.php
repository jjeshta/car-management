<?php

namespace App\Application\CommandHandler;

use App\Application\Command\RemoveServiceHistoryCommand;
use App\Application\Service\LoggerService;
use App\Domain\ServiceHistory\ServiceHistoryRepositoryInterface;

class RemoveServiceHistoryHandler
{
    public function __construct(
        private ServiceHistoryRepositoryInterface $serviceHistoryRepository,
        private readonly LoggerService $loggerService
    ) {}

    public function handle(RemoveServiceHistoryCommand $command): void
    {
        $serviceHistoryId = $command->getServiceHistoryId();
        $this->loggerService->logInfo("Attempting to remove service history with ID: {$serviceHistoryId}");
        try {
            $serviceHistory = $this->serviceHistoryRepository->find($command->getServiceHistoryId());

            if (!$serviceHistory) {
                $this->loggerService->logError("Service history not found with ID: {$serviceHistoryId}");
                throw new \InvalidArgumentException("Service history not found with ID: {$serviceHistoryId}");
            }

            $this->serviceHistoryRepository->remove($serviceHistory);
            $this->loggerService->logInfo("Service history with ID: {$serviceHistoryId} has been removed successfully.");
        } catch (\Throwable $e) {
            $this->loggerService->logError('An error occurred while removing the service history: ' . $e->getMessage());
            throw new \RuntimeException('An error occurred: service couldnot be removed ' . $e->getMessage());
        }
    }
}
