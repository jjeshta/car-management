<?php
namespace App\Application\CommandHandler;

use App\Application\Command\RemoveCarCommand;
use App\Domain\Car\CarRepositoryInterface;

class RemoveCarHandler
{
    public function __construct(
        private readonly CarRepositoryInterface $carRepository
    ) {}

    public function handle(RemoveCarCommand $command): void
    {
        try {
            $car = $this->carRepository->findByRegistrationNumber($command->getCarRegistrationNumber());

            if (!$car) {
                throw new \InvalidArgumentException('Car not found');
            }

            $this->carRepository->remove($car);
        } catch (\Throwable $e) {
            throw new \RuntimeException('An error occurred: ' . $e->getMessage());
        }
    }
}
