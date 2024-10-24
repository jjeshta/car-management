<?php

namespace App\Tests\Domain\ServiceHistory;

use App\Domain\ServiceHistory\ServiceHistory;
use App\Domain\Car\Car;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Mapping\Loader\AttributeLoader;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ServiceHistoryTest extends TestCase
{
    private ValidatorInterface $validator;
    private Car $car;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->addLoader(new AttributeLoader())
            ->getValidator();
        
        $this->car = $this->createMock(Car::class);
    }

    public function testServiceHistoryConstructorAndGetters(): void
    {
        $description = 'Oil change and tire replacement';
        $serviceDate = new \DateTime('2023-01-01');

        $serviceHistory = new ServiceHistory($description, $serviceDate, $this->car);

        $this->assertNull($serviceHistory->getId()); 
        $this->assertEquals($description, $serviceHistory->getDescription()); 
        $this->assertSame($serviceDate, $serviceHistory->getServiceDate());  
    }

    public function testServiceHistoryWithFutureDateThrowsValidationError(): void
    {
        $description = 'Routine maintenance';
        $serviceDate = new \DateTime('+1 day');

        $serviceHistory = new ServiceHistory($description, $serviceDate, $this->car);

        $violations = $this->validator->validate($serviceHistory);
        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals('Service date cannot be in the future.', $violations[0]->getMessage());
    }

    public function testServiceHistoryWithShortDescriptionThrowsValidationError(): void
    {
        $description = 'Short'; // Too short
        $serviceDate = new \DateTime('2023-01-01');

        $serviceHistory = new ServiceHistory($description, $serviceDate, $this->car);

        $violations = $this->validator->validate($serviceHistory);
        $this->assertGreaterThan(0, count($violations)); 
        $this->assertEquals('Description must be at least 10 characters long.', $violations[0]->getMessage());
    }

    public function testServiceHistoryWithBlankDescriptionThrowsValidationError(): void
    {
        $description = ''; 
        $serviceDate = new \DateTime('2023-01-01');

        $serviceHistory = new ServiceHistory($description, $serviceDate, $this->car);

        $violations = $this->validator->validate($serviceHistory);
        $this->assertGreaterThan(0, count($violations));
        $this->assertEquals('Description should not be blank.', $violations[0]->getMessage());
    }
}
