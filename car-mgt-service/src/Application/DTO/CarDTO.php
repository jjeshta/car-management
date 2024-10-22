<?php
namespace App\Application\DTO;

use App\Domain\Fitness;
use App\Domain\RoadTax;
use Symfony\Component\Validator\Constraints as Assert;

class CarDTO {
    #[Assert\NotBlank(message: "Make should not be blank.")]
    private string $make;

    #[Assert\NotBlank(message: "Model should not be blank.")]
    private string $model;

    #[Assert\NotBlank(message: "Registration number should not be blank.")]
    #[Assert\Regex(
        pattern: "/^\d{4} [A-Z]{2} \d{2}$/",
        message: "Registration number must follow the pattern '1234 AB 56'."
    )]
    private string $registrationNumber;

    private ?InsuranceDTO $insurance; // Optional insurance

    private ?FitnessDTO $fitness; // Optional fitness

    private ?RoadTaxDTO $roadTax; 
    
    public function __construct(
        string $make,
        string $model,
        string $registrationNumber,
        ?InsuranceDTO $insurance,
        ?FitnessDTO $fitness, 
        ?RoadTaxDTO $roadTax,
    ) {
        $this->make = $make;
        $this->model = $model;
        $this->registrationNumber = $registrationNumber;
        $this->insurance = $insurance;
        $this->fitness = $fitness;
        $this->roadTax = $roadTax;
    }

    public function getMake(): string {
        return $this->make;
    }

    public function getModel(): string {
        return $this->model;
    }

    public function getRegistrationNumber(): string {
        return $this->registrationNumber;
    }

    public function getInsurance(): ?InsuranceDTO {
        return $this->insurance;
    }

    public function getFitness(): ?FitnessDTO {
        return $this->fitness;
    }

    public function getRoadTax(): ?RoadTaxDTO {
        return $this->roadTax;
    }
}
