<?php
namespace App\Domain\ServiceHistory;

use App\Domain\Car\Car;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: "service_histories")]
class ServiceHistory {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: "Description should not be blank.")]
    #[Assert\Length(
        min: 10,
        minMessage: "Description must be at least {{ limit }} characters long."
    )]
    private string $description;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: "Service date is required.")]
    #[Assert\LessThanOrEqual(
        "today",
        message: "Service date cannot be in the future."
    )]
    private \DateTimeInterface $serviceDate;

    #[ORM\ManyToOne(targetEntity: Car::class, inversedBy: 'serviceHistories')]
    #[ORM\JoinColumn(nullable: false)]
    private Car $car;

    public function __construct(string $description, \DateTimeInterface $serviceDate, Car $car) {
        $this->description = $description;
        $this->serviceDate = $serviceDate;
        $this->car = $car;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getServiceDate(): \DateTimeInterface {
        return $this->serviceDate;
    }

}