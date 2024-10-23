<?php

namespace App\Application\CommandHandler;

use App\Application\Command\AddServiceHistoryCommand;
use App\Domain\ServiceHistory\ServiceHistory;
use App\Domain\ServiceHistory\ServiceHistoryRepositoryInterface;
use App\Domain\Car\CarRepositoryInterface;
use App\Trait\DateTimeConverterTrait;

class AddServiceHistoryHandler
{
    use DateTimeConverterTrait;

    public function __construct(
        private ServiceHistoryRepositoryInterface $serviceHistoryRepository,
        private CarRepositoryInterface $carRepository
    ) {}

    public function handle(AddServiceHistoryCommand $command): void
    {
        $serviceDTO = $command->getServiceHistoryDTO();

        $car = $this->carRepository->findByRegistrationNumber($serviceDTO->getCarRegistrationNumber());

        if (!$car) {
            throw new \InvalidArgumentException('Car Registration no. not found.');
        }

        try {
            $serviceHistory = new ServiceHistory(
                $serviceDTO->getDescription(),
                $this->convertToDateTime($serviceDTO->getDate()),
                 $car,
             );

            $this->serviceHistoryRepository->save($serviceHistory);
        } catch (\Throwable $e) {
            throw new \RuntimeException('An error occurred: ' . $e->getMessage());
        }
    }
}
