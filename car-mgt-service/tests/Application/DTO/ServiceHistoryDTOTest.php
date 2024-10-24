<?php

namespace App\Tests\Application\DTO;

use App\Application\DTO\ServiceHistoryDTO;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Mapping\Loader\AttributeLoader;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ServiceHistoryDTOTest extends TestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->addLoader(new AttributeLoader())
            ->getValidator();
    }

    public function testServiceHistoryDTOGetters(): void
    {
        $carRegistrationNumber = '1234 AB 56';
        $date = (new \DateTime())->format('Y-m-d H:i:s');
        $description = 'This is a valid description with enough length.';

        $serviceHistoryDTO = new ServiceHistoryDTO(
            $carRegistrationNumber,
            $date,
            $description
        );

        $violations = $this->validator->validate($serviceHistoryDTO);
        $this->assertCount(0, $violations);

        $this->assertEquals($carRegistrationNumber, $serviceHistoryDTO->getCarRegistrationNumber());
        $this->assertEquals($date, $serviceHistoryDTO->getDate());
        $this->assertEquals($description, $serviceHistoryDTO->getDescription());
    }
    
    public function testInvalidCarRegistrationNumber(): void
    {
        $serviceHistoryDTO = new ServiceHistoryDTO(
            'INVALID123',
            (new \DateTime())->format('Y-m-d H:i:s'),
            'This is a valid description with enough length.'
        );

        $violations = $this->validator->validate($serviceHistoryDTO);
        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals("Car registration number must follow the pattern '1234 AB 56'.", $violations[0]->getMessage());
    }

    public function testInvalidDateFormat(): void
    {
        $serviceHistoryDTO = new ServiceHistoryDTO(
            '1234 AB 56',
            'Invalid Date Format',
            'This is a valid description with enough length.'
        );

        $violations = $this->validator->validate($serviceHistoryDTO);
        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals("This value is not a valid datetime for 'date'. The correct format is Y-m-d H:i:s.", $violations[0]->getMessage());
    }

    public function testFutureDateValidation(): void
    {
        $serviceHistoryDTO = new ServiceHistoryDTO(
            '1234 AB 56',
            (new \DateTime())->modify('+1 day')->format('Y-m-d H:i:s'),
            'This is a valid description with enough length.'
        );

        $violations = $this->validator->validate($serviceHistoryDTO);
        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals("Date cannot be in the future.", $violations[0]->getMessage());
    }

    public function testDescriptionTooShort(): void
    {
        $serviceHistoryDTO = new ServiceHistoryDTO(
            '1234 AB 56',
            (new \DateTime())->format('Y-m-d H:i:s'),
            'Too short'
        );

        $violations = $this->validator->validate($serviceHistoryDTO);
        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals("Description must be at least 10 characters long.", $violations[0]->getMessage());
    }
}
