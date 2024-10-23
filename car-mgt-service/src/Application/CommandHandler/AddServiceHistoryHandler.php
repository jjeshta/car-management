<?php

namespace App\Application\CommandHandler;

use App\Application\Command\AddServiceHistoryCommand;
use App\Application\Service\LoggerService;
use App\Domain\ServiceHistory\ServiceHistory;
use App\Domain\ServiceHistory\ServiceHistoryRepositoryInterface;
use App\Domain\Car\CarRepositoryInterface;
use App\Trait\DateTimeConverterTrait;

class AddServiceHistoryHandler
{
    use DateTimeConverterTrait;

    public function __construct(
        private ServiceHistoryRepositoryInterface $serviceHistoryRepository,
        private CarRepositoryInterface $carRepository,
        private readonly LoggerService $loggerService
    ) {}

    public function handle(AddServiceHistoryCommand $command): void
    {
        $serviceDTO = $command->getServiceHistoryDTO();

        $this->loggerService->logInfo("Handling AddServiceHistoryCommand for car registration number: '{$serviceDTO->getCarRegistrationNumber()}'");

        $car = $this->carRepository->findByRegistrationNumber($serviceDTO->getCarRegistrationNumber());

        if (!$car) {
            $this->loggerService->logError("Car with registration number '{$serviceDTO->getCarRegistrationNumber()}' not found.");
            throw new \InvalidArgumentException('Car Registration no. not found.');
        }

        try {
            $this->loggerService->logInfo("Car with registration number '{$serviceDTO->getCarRegistrationNumber()}' found. Proceeding to add service history.");
            $serviceHistory = new ServiceHistory(
                $serviceDTO->getDescription(),
                $this->convertToDateTime($serviceDTO->getDate()),
                 $car,
             );

            $this->serviceHistoryRepository->save($serviceHistory);
            $this->loggerService->logInfo("Service history added successfully for car registration number: '{$serviceDTO->getCarRegistrationNumber()}'.");
        } catch (\Throwable $e) {
            $this->loggerService->logError("Exception thrown while adding service history for car registration number '{$serviceDTO->getCarRegistrationNumber()}': ". $e->getMessage());
            throw new \RuntimeException('An error occurred: ' . $e->getMessage());
        }
    }
}
