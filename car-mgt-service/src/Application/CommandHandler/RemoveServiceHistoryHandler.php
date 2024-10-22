<?php

namespace App\Application\ServiceHistory\Command;

use App\Domain\ServiceHistory\ServiceHistoryRepositoryInterface;
use App\Domain\ServiceHistory\ServiceHistory;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RemoveServiceHistoryHandler
{
    public function __construct(
        private ServiceHistoryRepositoryInterface $serviceHistoryRepository
    ) {}

    public function __invoke(RemoveServiceHistoryCommand $command): void
    {
        try {
            $serviceHistory = $this->serviceHistoryRepository->find($command->getServiceHistoryId());

            if ($serviceHistory instanceof ServiceHistory) {
                $this->serviceHistoryRepository->remove($serviceHistory);
            }
        } catch (\Throwable $e) {
            throw new \RuntimeException('An error occurred: ' . $e->getMessage());
        }
    }
}
