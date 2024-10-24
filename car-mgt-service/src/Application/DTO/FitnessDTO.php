<?php
namespace App\Application\DTO;
use App\Trait\DateTimeConverterTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class FitnessDTO
{
    use  DateTimeConverterTrait;

    #[Assert\NotBlank(message: "Issued date should not be blank.")]
    #[Assert\DateTime(format: "Y-m-d H:i:s", message: "This value is not a valid datetime. The correct format is Y-m-d H:i:s.")]
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

    #[Callback]
    public function validateIssuedDate(ExecutionContextInterface $context): void
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
            $context->buildViolation("Issued date cannot be in the future.")
                ->atPath('issued')
                ->addViolation();
        }
    }

}