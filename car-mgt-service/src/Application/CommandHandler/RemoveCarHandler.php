<?php
namespace App\Application\CommandHandler;

use App\Application\Command\RemoveCarCommand;
use App\Application\Service\LoggerService;
use App\Domain\Car\CarRepositoryInterface;

class RemoveCarHandler
{
    public function __construct(
        private readonly CarRepositoryInterface $carRepository,
        private readonly LoggerService $loggerService 
    ) {}

    public function handle(RemoveCarCommand $command): void
    {
        try {
            $this->loggerService->logInfo("Attempting to remove car with registration number: {$command->getCarRegistrationNumber()}");

            $car = $this->carRepository->findByRegistrationNumber($command->getCarRegistrationNumber());

            if (!$car) {
                $this->loggerService->logError("Car not found with registration number: {$command->getCarRegistrationNumber()}");
                throw new \InvalidArgumentException('Car not found');
            }

            $this->carRepository->remove($car);
            $this->loggerService->logInfo("Car with registration number: {$command->getCarRegistrationNumber()} has been removed successfully.");
        } catch (\Throwable $e) {
            $this->loggerService->logError('An error occurred while removing the car: ' . $e->getMessage());
            throw new \RuntimeException('An error occurred: ' . $e->getMessage());
        }
    }
}
