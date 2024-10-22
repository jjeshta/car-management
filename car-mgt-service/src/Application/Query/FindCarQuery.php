<?php 
namespace App\Application\Query;

use Symfony\Component\Validator\Constraints as Assert;

class FindCarQuery {
    #[Assert\NotBlank(message: "Registration number should not be blank.")]
    #[Assert\Regex(
        pattern: "/^\d{4} [A-Z]{2} \d{2}$/",
        message: "Registration number must follow the pattern '1234 AB 56' with exactly four digits, two uppercase letters, and two digits."
    )]
    private string $registrationNumber;

    public function __construct(string $registrationNumber) {
        $this->registrationNumber = $registrationNumber;
    }

    public function getRegistrationNumber(): string {
        return $this->registrationNumber;
    }
}
