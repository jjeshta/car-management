<?php

namespace App\Domain\Car;

use Doctrine\ORM\Mapping as ORM;
use App\Domain\Car\ValueObject\Insurance;
use App\Domain\Car\ValueObject\Fitness;
use App\Domain\Car\ValueObject\RoadTax;
use App\Domain\ServiceHistory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: "cars")]
class Car
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank(message: "Make should not be blank.")]
    private string $make;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank(message: "Model should not be blank.")]
    private string $model;

    #[ORM\Column(type: 'string', length: 20, unique: true)]
    #[Assert\NotBlank(message: "Registration number should not be blank.")]
    #[Assert\Regex(
        pattern: "/^\d{4} [A-Z]{2} \d{2}$/",
        message: "Registration number must follow the pattern '1234 AB 56' with exactly four digits, two uppercase letters, and two digits."
    )]
    private string $registrationNumber;

    #[ORM\OneToMany(targetEntity: ServiceHistory::class, mappedBy: 'car', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $serviceHistories;

    #[ORM\Embedded(class: Insurance::class)]
    private Insurance $insurance;

    #[ORM\Embedded(class: Fitness::class)]
    private Fitness $fitness;

    #[ORM\Embedded(class: RoadTax::class)]
    private RoadTax $roadTax;

    public function __construct(
        string $make,
        string $model,
        string $registrationNumber,
        Insurance $insurance,
        Fitness $fitness,
        RoadTax $roadTax
    ) {
        $this->make = $make;
        $this->model = $model;
        $this->registrationNumber = $registrationNumber;
        $this->insurance = $insurance;
        $this->fitness = $fitness;
        $this->roadTax = $roadTax;
        $this->serviceHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMake(): string
    {
        return $this->make;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getRegistrationNumber(): string
    {
        return $this->registrationNumber;
    }

    public function getInsurance(): Insurance
    {
        return $this->insurance;
    }

    public function getFitness(): Fitness
    {
        return $this->fitness;
    }

    public function getRoadTax(): RoadTax
    {
        return $this->roadTax;
    }

    public function addServiceHistory(ServiceHistory $serviceHistory): void
    {
        // Example additional logic: Prevent adding service histories in the future
        if ($serviceHistory->getServiceDate() > new \DateTime()) {
            throw new \Exception("Cannot add service history with a future date.");
        }
        if (!$this->serviceHistories->contains($serviceHistory)) {
            $this->serviceHistories[] = $serviceHistory;
        }
    }

    public function removeServiceHistory(ServiceHistory $serviceHistory): void
    {
        if ($this->serviceHistories->contains($serviceHistory)) {
            $this->serviceHistories->removeElement($serviceHistory);
        }
    }

    public function getServiceHistories(): Collection
    {
        return $this->serviceHistories;
    }

    public function listServiceHistories(): array
    {
        $histories = [];
        foreach ($this->serviceHistories as $history) {
            $histories[] = [
                'description' => $history->getDescription(),
                'serviceDate' => $history->getServiceDate()->format('Y-m-d'),
            ];
        }
        return $histories;
    }
}
