<?php
namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RoadTaxDTO
{
    #[Assert\NotBlank(message: "Issued date should not be blank.")]
    #[Assert\Date]
    private \DateTimeInterface $issued;

    #[Assert\NotBlank(message: "Valid until date should not be blank.")]
    #[Assert\Date]
    #[Assert\GreaterThan(propertyPath: "issued", message: "Valid until date must be later than the issued date.")]
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
}