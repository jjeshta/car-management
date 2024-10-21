<?php

namespace App\Domain\Car\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class RoadTax {

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank(message: "Issued date should not be blank.")]
    #[Assert\LessThanOrEqual(
        value: "today",
        message: "Issued date cannot be in the future."
    )]
    private \DateTimeInterface $issued;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank(message: "Valid until date should not be blank.")]
    #[Assert\GreaterThan(
        propertyPath: "issued",
        message: "Valid until date must be later than the issued date."
    )]
    private \DateTimeInterface $validUntil;

    public function __construct(\DateTimeInterface $issued, \DateTimeInterface $validUntil)
    {
        $this->issued = $issued;
        $this->validUntil = $validUntil;
    }

    public function getIssued(): \DateTimeInterface
    {
        return $this->issued;
    }

    public function getValidUntil(): \DateTimeInterface
    {
        return $this->validUntil;
    }

    public function isValid(): bool
    {
        return new \DateTime() <= $this->validUntil;
    }
}