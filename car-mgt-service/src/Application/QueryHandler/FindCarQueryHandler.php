<?php

namespace App\Application\QueryHandler;

use App\Application\Query\FindCarQuery;
use App\Domain\Car\CarRepositoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class FindCarHandler {
    private CarRepositoryInterface $carRepository;
    private ValidatorInterface $validator;

    public function __construct(CarRepositoryInterface $carRepository, ValidatorInterface $validator) {
        $this->carRepository = $carRepository;
        $this->validator = $validator;
    }

    public function handle(FindCarQuery $query) {
        $errors = $this->validator->validate($query);
        if (count($errors) > 0) {
            throw new ValidationFailedException($query, $errors);
        }

        try {
            $car = $this->carRepository->findByRegistrationNumber($query->getRegistrationNumber());
            if (!$car) {
                throw new \Exception('Car not found.');
            }

            return $car;
        } catch (\Exception $e) {
            throw new \RuntimeException('An error occurred while finding the car: ' . $e->getMessage());
        }
    }
}