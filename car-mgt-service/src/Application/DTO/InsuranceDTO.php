<?php

namespace App\Application\DTO;

use App\Trait\DateTimeConverterTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class InsuranceDTO
{
    use  DateTimeConverterTrait;

    #[Assert\NotBlank(message: "Insurer should not be blank.")]
    private string $insurer;

    #[Assert\NotBlank(message: "Policy number should not be blank.")]
    private string $policyNumber;

    #[Assert\NotBlank(message: "Date issued should not be blank.")]
    #[Assert\DateTime(format: "Y-m-d H:i:s", message: "This value is not a valid datetime. The correct format is Y-m-d H:i:s.")]
    #[Assert\LessThan(propertyPath: "dateExpiry", message: "Date issued must be less than date expiry.")]
    #[Assert\LessThanOrEqual(propertyPath: "dateStart", message: "Date issued must not be greater than date start.")]
    private string $dateIssued;

    #[Assert\NotBlank(message: "Date expiry should not be blank.")]
    #[Assert\DateTime(format: "Y-m-d H:i:s", message: "This value is not a valid datetime. The correct format is Y-m-d H:i:s.")]
    #[Assert\GreaterThan(propertyPath: "dateIssued", message: "Date expiry must be later than date issued.")]
    private string $dateExpiry;

    #[Assert\NotBlank(message: "Date start should not be blank.")]
    #[Assert\DateTime(format: "Y-m-d H:i:s", message: "This value is not a valid datetime. The correct format is Y-m-d H:i:s.")]
    #[Assert\NotEqualTo(propertyPath: "dateExpiry", message: "Date start cannot be equal to date expiry.")]
    #[Assert\LessThan(propertyPath: "dateExpiry", message: "Date start cannot be greater than date expiry.")]
    private string $dateStart;

    public function __construct(string $insurer, string $policyNumber, string $dateIssued, string $dateExpiry, string $dateStart)
    {
        $this->insurer = $insurer;
        $this->policyNumber = $policyNumber;
        $this->dateIssued = $dateIssued;
        $this->dateExpiry = $dateExpiry;
        $this->dateStart = $dateStart;
    }

    public function getInsurer(): string
    {
        return $this->insurer;
    }

    public function getPolicyNumber(): string
    {
        return $this->policyNumber;
    }

    public function getDateIssued(): string
    {
        return $this->dateIssued;
    }

    public function getDateExpiry(): string
    {
        return $this->dateExpiry;
    }

    public function getDateStart(): string
    {
        return $this->dateStart;
    }

    #[Callback]
    public function validateDates(ExecutionContextInterface $context): void
    {
        if (!$this->isValidDate($this->dateIssued)) {
            $context->buildViolation("This value is not a valid datetime for 'dateIssued'. The correct format is Y-m-d H:i:s.")
                ->atPath('dateIssued')
                ->addViolation();
            return;
        }

        $issuedDate = new \DateTime($this->dateIssued);
        $today = new \DateTime();

        if ($issuedDate > $today) {
            $context->buildViolation("Date issued cannot be in the future.")
                ->atPath('issued')
                ->addViolation();
        }
    }

}
