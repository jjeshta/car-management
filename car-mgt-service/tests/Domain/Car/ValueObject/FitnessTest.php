<?php

namespace App\Tests\Domain\Car\ValueObject;

use App\Domain\Car\ValueObject\Fitness;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Mapping\Loader\AttributeLoader;

class FitnessTest extends TestCase
{
    private $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->addLoader(new AttributeLoader())
            ->getValidator();
    }

    public function testValidFitness(): void
    {
        $issued = new \DateTime('2023-01-01');
        $validUntil = new \DateTime('2024-01-01');

        $fitness = new Fitness($issued, $validUntil);

        $this->assertSame($issued, $fitness->getIssued());
        $this->assertSame($validUntil, $fitness->getValidUntil()); 
        $this->assertFalse($fitness->isValid());

        $violations = $this->validator->validate($fitness);
        $this->assertCount(0, $violations);
    }

    public function testInvalidFutureIssuedDate(): void
    {
        $issued = new \DateTime('+1 day');
        $validUntil = new \DateTime('+1 year');

        $fitness = new Fitness($issued, $validUntil);
   
        $violations = $this->validator->validate($fitness);
        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals('Issued date cannot be in the future.', $violations[0]->getMessage());
    }

    public function testValidUntilBeforeIssuedDate(): void
    {
        $issued = new \DateTime('2023-01-01');
        $validUntil = new \DateTime('2022-12-31');

        $fitness = new Fitness($issued, $validUntil);

        $violations = $this->validator->validate($fitness);
        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals('Valid until date must be later than the issued date.', $violations[0]->getMessage());
    }
}
