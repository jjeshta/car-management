<?php

namespace App\Application\CommandHandler;

use App\Application\Command\UpdateCarCommand;
use App\Application\Service\LoggerService;
use App\Domain\Car\CarRepositoryInterface;

class UpdateCarHandler
{
    public function __construct(
        private readonly CarRepositoryInterface $carRepository,
        private readonly LoggerService $loggerService
    ) {}

    public function handle(UpdateCarCommand $command): void
    {

        $this->loggerService->logInfo("Attempting to update car with registration number: {$command->getRegistrationNumber()}");

        try {
            $car = $this->carRepository->findByRegistrationNumber($command->getRegistrationNumber());

            if (!$car) {
                $this->loggerService->logError("Car not found with registration number: {$command->getRegistrationNumber()}");
                throw new \InvalidArgumentException('Car not found');
            }

            $car->updateDetails($command->getMake(), $command->getModel(), $command->getRegistrationNumber());

            $this->carRepository->save($car);
            $this->loggerService->logInfo("Car with registration number: {$command->getRegistrationNumber()} has been updated successfully.");
        } catch (\Throwable $e) {
            $this->loggerService->logError('An error occurred while updating the car: ' . $e->getMessage());
            throw new \RuntimeException('An error occurred: ' . $e->getMessage());
        }
    }
}
