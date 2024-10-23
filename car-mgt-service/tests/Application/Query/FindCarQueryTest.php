<?php

namespace App\Tests\Application\Query;

use App\Application\Query\FindCarQuery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class FindCarQueryTest extends TestCase
{
    private $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()->getValidator();
    }

    public function testValidRegistrationNumber(): void
    {
        $registrationNumber = '1234 AB 56';
        $query = new FindCarQuery($registrationNumber);

        $violations = $this->validator->validate($query);

        $this->assertCount(0, $violations);
    }

    public function testRegistrationNumberCannotBeBlank(): void
    {
        $registrationNumber = '';
        $query = new FindCarQuery($registrationNumber);

        $violations = $this->validator->validate($query);

        $this->assertGreaterThan(0, $violations);
        if ($violations->count() > 0) {
            $this->assertEquals("Registration number should not be blank.", $violations[0]->getMessage());
        }
    }

    public function testInvalidPatternRegistrationNumber(): void
    {
        $registrationNumber = 'ABCD 12 34';
        $query = new FindCarQuery($registrationNumber);

        $violations = $this->validator->validate($query);

        $this->assertGreaterThan(0, $violations);
        if ($violations->count() > 0) {
            $this->assertEquals(
                "Registration number must follow the pattern '1234 AB 56' with exactly four digits, two uppercase letters, and two digits.",
                $violations[0]->getMessage()
            );
        }
    }

    public function testAnotherInvalidPatternRegistrationNumber(): void
    {
        $registrationNumber = '123 AB 5';
        $query = new FindCarQuery($registrationNumber);

        $violations = $this->validator->validate($query);

        $this->assertGreaterThan(0, $violations);
        if ($violations->count() > 0) {
            $this->assertEquals(
                "Registration number must follow the pattern '1234 AB 56' with exactly four digits, two uppercase letters, and two digits.",
                $violations[0]->getMessage()
            );
        }
    }
}
