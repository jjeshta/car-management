<?php

namespace App\Application\DTO;

use App\Trait\DateTimeConverterTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ServiceHistoryDTO
{
    use DateTimeConverterTrait;

    #[Assert\NotBlank(message: "Car registration number should not be blank.")]
    #[Assert\Regex(
        pattern: "/^\d{4} [A-Z]{2} \d{2}$/",
        message: "Car registration number must follow the pattern '1234 AB 56'."
    )]
    private string $carRegistrationNumber;

    #[Assert\NotBlank(message: "Date should not be blank.")]
    #[Assert\DateTime(format: "Y-m-d H:i:s", message: "This value is not a valid datetime. The correct format is Y-m-d H:i:s.")]
    private string $date;

    #[Assert\NotBlank(message: "Description should not be blank.")]
    #[Assert\Length(
        min: 10,
        minMessage: "Description must be at least {{ limit }} characters long."
    )]
    private string $description;

    public function __construct(
        string $carRegistrationNumber,
        string $date,
        string $description
    ) {
        $this->carRegistrationNumber = $carRegistrationNumber;
        $this->date = $date;
        $this->description = $description;
    }

    public function getCarRegistrationNumber(): string
    {
        return $this->carRegistrationNumber;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    #[Callback]
    public function validateDates(ExecutionContextInterface $context): void
    {
        if (!$this->isValidDate($this->date)) {
            $context->buildViolation("This value is not a valid datetime for 'date'. The correct format is Y-m-d H:i:s.")
                ->atPath('date')
                ->addViolation();
            return;
        }

        $issuedDate = new \DateTime($this->date);
        $today = new \DateTime();

        if ($issuedDate > $today) {
            $context->buildViolation("Date cannot be in the future.")
                ->atPath('date')
                ->addViolation();
        }
    }
}
