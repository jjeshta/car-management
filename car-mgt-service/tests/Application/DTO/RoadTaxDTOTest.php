<?php

namespace App\Tests\Application\DTO;

use App\Application\DTO\RoadTaxDTO;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Mapping\Loader\AttributeLoader;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RoadTaxDTOTest extends TestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->addLoader(new AttributeLoader())
            ->getValidator();
    }

    public function testValidRoadTaxDTO(): void
    {
        $roadTaxDTO = new RoadTaxDTO(
            (new \DateTime())->format('Y-m-d H:i:s'),
            (new \DateTime())->modify('+1 year')->format('Y-m-d H:i:s')
        );

        $violations = $this->validator->validate($roadTaxDTO);
        $this->assertCount(0, $violations);
    }

    public function testInvalidIssuedDateInFuture(): void
    {
        $roadTaxDTO = new RoadTaxDTO(
            (new \DateTime())->modify('+1 day')->format('Y-m-d H:i:s'),
            (new \DateTime())->modify('+1 year')->format('Y-m-d H:i:s')
        );

        $violations = $this->validator->validate($roadTaxDTO);
        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals("Date issued cannot be in the future.", $violations[0]->getMessage());
    }

    public function testValidUntilDateBeforeIssuedDate(): void
    {
        $roadTaxDTO = new RoadTaxDTO(
            (new \DateTime())->format('Y-m-d H:i:s'),
            (new \DateTime())->modify('-1 day')->format('Y-m-d H:i:s')
        );

        $violations = $this->validator->validate($roadTaxDTO);
        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals("Valid until date must be later than the issued date.", $violations[0]->getMessage());
    }

    public function testInvalidDateFormatForIssued(): void
    {
        $roadTaxDTO = new RoadTaxDTO(
            'Invalid Date Format',
            (new \DateTime())->modify('+1 year')->format('Y-m-d H:i:s')
        );

        $violations = $this->validator->validate($roadTaxDTO);
        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals("This value is not a valid datetime for 'issued'. The correct format is Y-m-d H:i:s.", $violations[0]->getMessage());
    }

    public function testBlankValidUntilDate(): void
    {
        $roadTaxDTO = new RoadTaxDTO(
            (new \DateTime())->format('Y-m-d H:i:s'),
            ''
        );

        $violations = $this->validator->validate($roadTaxDTO);
        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals("Valid until date should not be blank.", $violations[0]->getMessage());
    }
}
