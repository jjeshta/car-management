<?php

namespace App\Application\CommandHandler;

use App\Application\Command\AddCarCommand;
use App\Application\DTO\FitnessDTO;
use App\Application\DTO\InsuranceDTO;
use App\Application\DTO\RoadTaxDTO;
use App\Domain\Car\Car;
use App\Domain\Car\CarRepositoryInterface;
use App\Domain\Car\ValueObject\Insurance;
use App\Domain\Car\ValueObject\Fitness;
use App\Domain\Car\ValueObject\RoadTax;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class AddCarHandler
{
    public function __construct(private CarRepositoryInterface $carRepository, private ValidatorInterface $validator)
    {}

    public function __invoke(AddCarCommand $command): int
    {
        $carDTO = $command->getCarDTO();

        $errors = $this->validator->validate($carDTO);
        if (count($errors) > 0) {
            throw new ValidationFailedException($carDTO, $errors);
        }

        try {
            $car = new Car(
                $carDTO->getMake(),
                $carDTO->getModel(),
                $carDTO->getRegistrationNumber(),
                $this->createInsurance($carDTO->getInsurance()),
                $this->createFitness($carDTO->getFitness()),
                $this->createRoadTax($carDTO->getRoadTax())
            );

            $this->carRepository->save($car);
            return $car->getId();
        } catch (\Exception $e) {
            throw new \RuntimeException('An error occurred: ' . $e->getMessage());
        }
    }

    private function createInsurance(?InsuranceDTO $insuranceDTO): ?Insurance
    {
        if ($insuranceDTO === null) {
            return null;
        }

        return new Insurance(
            $insuranceDTO->getInsurer(),
            $insuranceDTO->getPolicyNumber(),
            $insuranceDTO->getDateIssued(),
            $insuranceDTO->getDateExpiry(),
            $insuranceDTO->getDateStart()
        );
    }

    private function createFitness(?FitnessDTO $fitnessDTO): ?Fitness
    {
        if ($fitnessDTO === null) {
            return null;
        }

        return new Fitness(
            $fitnessDTO->getIssued(),
            $fitnessDTO->getValidUntil()
        );
    }

    private function createRoadTax(?RoadTaxDTO $roadTaxDTO): ?RoadTax
    {
        if ($roadTaxDTO === null) {
            return null;
        }

        return new RoadTax(
            $roadTaxDTO->getIssued(),
            $roadTaxDTO->getValidUntil()
        );
    }
}
