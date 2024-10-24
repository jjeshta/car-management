<?php
namespace App\Application\DTO;

use App\Trait\DateTimeConverterTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RoadTaxDTO
{
    use  DateTimeConverterTrait;

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
        if (!$this->isValidDate($this->issued)) {
            $context->buildViolation("This value is not a valid datetime for 'issued'. The correct format is Y-m-d H:i:s.")
                ->atPath('issued')
                ->addViolation();
            return;
        }

        $issuedDate = new \DateTime($this->issued);
        $today = new \DateTime();

        if ($issuedDate > $today) {
            $context->buildViolation("Date issued cannot be in the future.")
                ->atPath('issued')
                ->addViolation();
        }
    }
}