<?php
namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class FitnessDTO
{
    #[Assert\NotBlank(message: "Issued date should not be blank.")]
    #[Assert\DateTime(format: "Y-m-d H:i:s", message: "This value is not a valid datetime. The correct format is Y-m-d H:i:s.")]
    #[Assert\LessThanOrEqual(
        value: "today",
        message: "Issued date cannot be in the future."
    )]
    private ?string $issued = null;

    #[Assert\NotBlank(message: "Valid until date should not be blank.")]
    #[Assert\DateTime(format: "Y-m-d H:i:s", message: "This value is not a valid datetime. The correct format is Y-m-d H:i:s.")]
    #[Assert\GreaterThan(
        propertyPath: "issued",
        message: "Valid until date must be later than the issued date."
    )]
    private ?string $validUntil = null;
    
    public function __construct(?string $issued = null, ?string $validUntil = null)
    {
        $this->issued = $issued;
        $this->validUntil = $validUntil;
    }

    public function getIssued(): ?string
    {
        return $this->issued;
    }

    public function getValidUntil(): ?string
    {
        return $this->validUntil;
    }
}