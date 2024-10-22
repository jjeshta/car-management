<?php

namespace App\Application\CommandHandler;

use App\Application\Command\UpdateCarCommand;
use App\Domain\Car\CarRepositoryInterface;

class UpdateCarHandler
{
    public function __construct(
        private readonly CarRepositoryInterface $carRepository
    ) {}

    public function handle(UpdateCarCommand $command): void
    {
        try {
            $car = $this->carRepository->findByRegistrationNumber($command->getRegistrationNumber());

            if (!$car) {
                throw new \InvalidArgumentException('Car not found');
            }

            $car->updateDetails($command->getMake(), $command->getModel(), $command->getRegistrationNumber());

            $this->carRepository->save($car);
        } catch (\Throwable $e) {
            throw new \RuntimeException('An error occurred: ' . $e->getMessage());
        }
    }
}
