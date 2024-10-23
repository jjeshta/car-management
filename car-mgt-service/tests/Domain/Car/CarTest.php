<?php

namespace App\Tests\Domain\Car;

use App\Domain\Car\Car;
use App\Domain\Car\ValueObject\Insurance;
use App\Domain\Car\ValueObject\Fitness;
use App\Domain\Car\ValueObject\RoadTax;
use App\Domain\ServiceHistory\ServiceHistory;
use PHPUnit\Framework\TestCase;

class CarTest extends TestCase
{
    private $insurance;
    private $fitness;
    private $roadTax;
    private $car;

    protected function setUp(): void
    {
        $this->insurance = $this->createMock(Insurance::class);
        $this->fitness = $this->createMock(Fitness::class);
        $this->roadTax = $this->createMock(RoadTax::class);

        $this->insurance->method('isValid')->willReturn(true);
        $this->fitness->method('isValid')->willReturn(true);
        $this->roadTax->method('isValid')->willReturn(true);

        $this->car = new Car('TestMake', 'TestModel', '1234 AB 56', $this->insurance, $this->fitness, $this->roadTax);
    }

    public function testCarInitialization(): void
    {
        $this->assertEquals('TestMake', $this->car->getMake());
        $this->assertEquals('TestModel', $this->car->getModel());
        $this->assertEquals('1234 AB 56', $this->car->getRegistrationNumber());
    }

    public function testIsFitForRoad(): void
    {
        $this->assertTrue($this->car->isFitForRoad());
    }

    public function testAddServiceHistory(): void
    {
        $serviceHistory = $this->createMock(ServiceHistory::class);
        $serviceHistory->method('getServiceDate')->willReturn(new \DateTime('yesterday'));

        $this->car->addServiceHistory($serviceHistory);
        $this->assertCount(1, $this->car->getServiceHistories());
    }

    public function testAddFutureServiceHistory(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Cannot add service history with a future date.");

        $serviceHistory = $this->createMock(ServiceHistory::class);
        $serviceHistory->method('getServiceDate')->willReturn(new \DateTime('tomorrow'));

        $this->car->addServiceHistory($serviceHistory);
    }

    public function testUpdateDetails(): void
    {
        $this->car->updateDetails('NewMake', 'NewModel');
        $this->assertEquals('NewMake', $this->car->getMake());
        $this->assertEquals('NewModel', $this->car->getModel());
    }

    public function testCannotAddServiceHistoryWithFutureDate(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Cannot add service history with a future date.");

        $futureDate = new \DateTime('+1 year');
        $serviceHistory = new ServiceHistory('Future service date', $futureDate, $this->car);

        $this->car->addServiceHistory($serviceHistory);
    }
}
