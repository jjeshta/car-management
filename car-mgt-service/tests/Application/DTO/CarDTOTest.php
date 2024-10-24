<?php

namespace App\Tests\Application\DTO;

use App\Application\DTO\CarDTO;
use App\Application\DTO\InsuranceDTO;
use App\Application\DTO\FitnessDTO;
use App\Application\DTO\RoadTaxDTO;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class CarDTOTest extends TestCase
{
    private $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()->getValidator();
    }

    public function testValidCarDTO(): void
    {
        $insurance = $this->createMock(InsuranceDTO::class);
        $fitness = $this->createMock(FitnessDTO::class);
        $roadTax = $this->createMock(RoadTaxDTO::class);

        $carDTO = new CarDTO(
            'Toyota',
            'Corolla',
            '1234 AB 56',
            $insurance,
            $fitness,
            $roadTax
        );

        $violations = $this->validator->validate($carDTO);

        $this->assertCount(0, $violations);
    }

    public function testMakeCannotBeBlank(): void
    {
        $insurance = $this->createMock(InsuranceDTO::class);
        $fitness = $this->createMock(FitnessDTO::class);
        $roadTax = $this->createMock(RoadTaxDTO::class);

        $carDTO = new CarDTO(
            '',
            'Corolla',
            '1234 AB 56',
            $insurance,
            $fitness,
            $roadTax
        );

        $violations = $this->validator->validate($carDTO);

        $this->assertGreaterThan(0, $violations);
        if ($violations->count() > 0) {
            $this->assertEquals("Make should not be blank.", $violations[0]->getMessage());
        }
    }

    public function testModelCannotBeBlank(): void
    {
        $insurance = $this->createMock(InsuranceDTO::class);
        $fitness = $this->createMock(FitnessDTO::class);
        $roadTax = $this->createMock(RoadTaxDTO::class);

        $carDTO = new CarDTO(
            'Toyota',
            '',
            '1234 AB 56',
            $insurance,
            $fitness,
            $roadTax
        );

        $violations = $this->validator->validate($carDTO);

        $this->assertGreaterThan(0, $violations);
        if ($violations->count() > 0) {
            $this->assertEquals("Model should not be blank.", $violations[0]->getMessage());
        }
    }

    public function testRegistrationNumberCannotBeBlank(): void
    {
        $insurance = $this->createMock(InsuranceDTO::class);
        $fitness = $this->createMock(FitnessDTO::class);
        $roadTax = $this->createMock(RoadTaxDTO::class);

        $carDTO = new CarDTO(
            'Toyota',
            'Corolla',
            '', 
            $insurance,
            $fitness,
            $roadTax
        );

        $violations = $this->validator->validate($carDTO);

        $this->assertGreaterThan(0, $violations);
        if ($violations->count() > 0) {
            $this->assertEquals("Registration number should not be blank.", $violations[0]->getMessage());
        }
    }

    public function testRegistrationNumberPatternInvalid(): void
    {
        $insurance = $this->createMock(InsuranceDTO::class);
        $fitness = $this->createMock(FitnessDTO::class);
        $roadTax = $this->createMock(RoadTaxDTO::class);

        $carDTO = new CarDTO(
            'Toyota',
            'Corolla',
            'ABCD 12 34',
            $insurance,
            $fitness,
            $roadTax
        );

        $violations = $this->validator->validate($carDTO);

        $this->assertGreaterThan(0, $violations);
        if ($violations->count() > 0) {
            $this->assertEquals(
                "Registration number must follow the pattern '1234 AB 56'.",
                $violations[0]->getMessage()
            );
        }
    }
}
