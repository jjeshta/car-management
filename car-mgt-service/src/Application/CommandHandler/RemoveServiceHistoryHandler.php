<?php

namespace App\Application\CommandHandler;

use App\Application\Command\RemoveServiceHistoryCommand;
use App\Domain\ServiceHistory\ServiceHistoryRepositoryInterface;
use App\Domain\ServiceHistory\ServiceHistory;

class RemoveServiceHistoryHandler
{
    public function __construct(
        private ServiceHistoryRepositoryInterface $serviceHistoryRepository
    ) {}

    public function handle(RemoveServiceHistoryCommand $command): void
    {
        try {
            $serviceHistory = $this->serviceHistoryRepository->find($command->getServiceHistoryId());

            if ($serviceHistory instanceof ServiceHistory) {
                $this->serviceHistoryRepository->remove($serviceHistory);
            }
        } catch (\Throwable $e) {
            throw new \RuntimeException('An error occurred: service couldnot be removed ' . $e->getMessage());
        }
    }
}
