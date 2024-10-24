<?php

namespace App\Tests\Application\DTO;

use App\Application\DTO\FitnessDTO;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Mapping\Loader\AttributeLoader;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FitnessDTOTest extends TestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->addLoader(new AttributeLoader())
            ->getValidator();
    }

    public function testValidFitnessDTO(): void
    {
        $fitnessDTO = new FitnessDTO(
            (new \DateTime())->format('Y-m-d H:i:s'),  // Issued today
            (new \DateTime())->modify('+1 year')->format('Y-m-d H:i:s')  // Valid until next year
        );

        $violations = $this->validator->validate($fitnessDTO);

        $this->assertCount(0, $violations);
    }

    public function testIssuedDateCannotBeInTheFuture(): void
    {
        $fitnessDTO = new FitnessDTO(
            (new \DateTime())->modify('+1 day')->format('Y-m-d H:i:s'),  // Issued tomorrow
            (new \DateTime())->modify('+1 year')->format('Y-m-d H:i:s')  // Valid until next year
        );

        $violations = $this->validator->validate($fitnessDTO);

        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals("Issued date cannot be in the future.", $violations[0]->getMessage());
    }

    public function testValidUntilDateMustBeLaterThanIssuedDate(): void
    {
        $fitnessDTO = new FitnessDTO(
            (new \DateTime())->format('Y-m-d H:i:s'), 
            (new \DateTime())->modify('-1 day')->format('Y-m-d H:i:s')
        );

        $violations = $this->validator->validate($fitnessDTO);

        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals("Valid until date must be later than the issued date.", $violations[0]->getMessage());
    }

    public function testInvalidDateFormatForIssued(): void
    {
        $fitnessDTO = new FitnessDTO(
            'Invalid Date Format', 
            (new \DateTime())->modify('+1 year')->format('Y-m-d H:i:s')
        );

        $violations = $this->validator->validate($fitnessDTO);

        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals("This value is not a valid datetime for 'issued'. The correct format is Y-m-d H:i:s.", $violations[0]->getMessage());
    }
}
