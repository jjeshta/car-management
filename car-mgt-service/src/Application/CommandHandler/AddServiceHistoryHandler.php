<?php

namespace App\Application\CommandHandler;

use App\Application\Command\AddServiceHistoryCommand;
use App\Domain\ServiceHistory\ServiceHistory;
use App\Domain\ServiceHistory\ServiceHistoryRepositoryInterface;
use App\Domain\Car\CarRepositoryInterface;
use App\Trait\DateTimeConverterTrait;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class AddServiceHistoryHandler
{
    use DateTimeConverterTrait;

    public function __construct(
        private ServiceHistoryRepositoryInterface $serviceHistoryRepository,
        private CarRepositoryInterface $carRepository,
        private ValidatorInterface $validator
    ) {}

    public function handle(AddServiceHistoryCommand $command): void
    {
        $serviceDTO = $command->getServiceHistoryDTO();

        $errors = $this->validator->validate($serviceDTO);
        if (count($errors) > 0) {
            throw new ValidationFailedException($serviceDTO, $errors);
        }

        $car = $this->carRepository->findByRegistrationNumber($serviceDTO->getCarRegistrationNumber());

        if (!$car) {
            throw new \InvalidArgumentException('Car not found.');
        }

        $serviceHistory = new ServiceHistory(
           $serviceDTO->getDescription(),
           $this->convertToDateTime($serviceDTO->getDate()),
            $car,
        );

        try {
            $this->serviceHistoryRepository->save($serviceHistory);
        } catch (\Throwable $e) {
            throw new \RuntimeException('An error occurred: ' . $e->getMessage());
        }
    }
}
