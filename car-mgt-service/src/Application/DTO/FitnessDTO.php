<?php
namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class FitnessDTO
{
    #[Assert\NotBlank(message: "Issued date should not be blank.")]
    #[Assert\Date]
    #[Assert\LessThanOrEqual(
        value: "today",
        message: "Issued date cannot be in the future."
    )]
    private ?\DateTimeInterface $issued = null;

    #[Assert\NotBlank(message: "Valid until date should not be blank.")]
    #[Assert\Date]
    #[Assert\GreaterThan(
        propertyPath: "issued",
        message: "Valid until date must be later than the issued date."
    )]
    private ?\DateTimeInterface $validUntil = null;
    
    public function __construct(?\DateTimeInterface $issued = null, ?\DateTimeInterface $validUntil = null)
    {
        $this->issued = $issued;
        $this->validUntil = $validUntil;
    }

    public function getIssued(): ?\DateTimeInterface
    {
        return $this->issued;
    }

    public function getValidUntil(): ?\DateTimeInterface
    {
        return $this->validUntil;
    }
}