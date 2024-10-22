<?php
namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RoadTaxDTO
{
    #[Assert\NotBlank(message: "Issued date should not be blank.")]
    #[Assert\DateTime]
    #[Assert\LessThanOrEqual(value: "today", message: "Date issued cannot be in the future.")]
    private string $issued;

    #[Assert\NotBlank(message: "Valid until date should not be blank.")]
    #[Assert\DateTime]
    #[Assert\GreaterThan(propertyPath: "issued", message: "Valid until date must be later than the issued date.")]
    private string $validUntil;

    public function __construct(string $issued, string $validUntil)
    {
        $this->issued = $issued;
        $this->validUntil = $validUntil;
    }

    public function getIssued(): string
    {
        return $this->issued;
    }

    public function getValidUntil(): string
    {
        return $this->validUntil;
    }

    #[Callback]
    public function validateDates(ExecutionContextInterface $context): void
    {
        $issuedDate = new \DateTime($this->issued);
        $today = new \DateTime();

        if ($issuedDate > $today) {
            $context->buildViolation("Date issued cannot be in the future for road tax")
                ->atPath('issued')
                ->addViolation();
        }
    }
}