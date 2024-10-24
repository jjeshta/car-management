<?php

namespace App\Tests\Application\DTO;

use App\Application\DTO\InsuranceDTO;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Mapping\Loader\AttributeLoader;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class InsuranceDTOTest extends TestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
                ->addLoader(new AttributeLoader())
            ->getValidator();
    }

    public function testValidInsuranceDTO(): void
    {
        $insuranceDTO = new InsuranceDTO(
            'Valid Insurer',
            '123456',
            (new \DateTime())->format('Y-m-d H:i:s'),
            (new \DateTime())->modify('+1 year')->format('Y-m-d H:i:s'),
            (new \DateTime())->modify('+1 day')->format('Y-m-d H:i:s')
        );

        $violations = $this->validator->validate($insuranceDTO);

        $this->assertCount(0, $violations);
    }

    public function testInvalidInsuranceDTOWithFutureDateIssued(): void
    {
        $insuranceDTO = new InsuranceDTO(
            'Valid Insurer',
            '123456',
            (new \DateTime())->modify('+1 day')->format('Y-m-d H:i:s'),
            (new \DateTime())->modify('+1 year')->format('Y-m-d H:i:s'),
            (new \DateTime())->modify('+1 day')->format('Y-m-d H:i:s')
        );

        $violations = $this->validator->validate($insuranceDTO);

        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals("Date issued cannot be in the future.", $violations[0]->getMessage());
    }

    public function testInvalidInsuranceDTOWithWrongDateFormat(): void
    {
        $insuranceDTO = new InsuranceDTO(
            'Valid Insurer',
            '123456',
            'Invalid Date Format', 
            (new \DateTime())->modify('+1 year')->format('Y-m-d H:i:s'),
            (new \DateTime())->modify('+1 day')->format('Y-m-d H:i:s')
        );
    
        $violations = $this->validator->validate($insuranceDTO);
    
        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals("This value is not a valid datetime for 'dateIssued'. The correct format is Y-m-d H:i:s.", $violations[0]->getMessage());
    }
   
    public function testInvalidInsuranceDTOWithDateExpiryBeforeDateIssued(): void
    {
        $insuranceDTO = new InsuranceDTO(
            'Valid Insurer',
            '123456',
            (new \DateTime())->format('Y-m-d H:i:s'), 
            (new \DateTime())->modify('-1 day')->format('Y-m-d H:i:s'),
            (new \DateTime())->format('Y-m-d H:i:s')
        );

        $violations = $this->validator->validate($insuranceDTO);

        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals("Date issued must be less than date expiry.", $violations[0]->getMessage());
    }
}
