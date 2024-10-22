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
use App\Trait\DateTimeConverterTrait;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class AddCarHandler
{   
    use DateTimeConverterTrait;

    public function __construct(private CarRepositoryInterface $carRepository, private ValidatorInterface $validator)
    {}

    public function handle(AddCarCommand $command): ?int
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
                $carDTO->getInsurance() !== null ? $this->createInsurance($carDTO->getInsurance()) : null,
                $carDTO->getFitness() !== null ? $this->createFitness($carDTO->getFitness()) : null,
                $carDTO->getRoadTax() !== null ? $this->createRoadTax($carDTO->getRoadTax()) : null
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
            $this->convertToDateTime($insuranceDTO->getDateIssued()),
            $this->convertToDateTime($insuranceDTO->getDateExpiry()),
            $this->convertToDateTime($insuranceDTO->getDateStart())
        );
    }

    private function createFitness(?FitnessDTO $fitnessDTO): ?Fitness
    {
        if ($fitnessDTO === null) {
            return null;
        }

        return new Fitness(
             $this->convertToDateTime($fitnessDTO->getIssued()),
             $this->convertToDateTime($fitnessDTO->getValidUntil())
        );
    }

    private function createRoadTax(?RoadTaxDTO $roadTaxDTO): ?RoadTax
    {
        if ($roadTaxDTO === null) {
            return null;
        }

        return new RoadTax(
            $this->convertToDateTime($roadTaxDTO->getIssued()),
            $this->convertToDateTime($roadTaxDTO->getValidUntil())
        );
    }
}
