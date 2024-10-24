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
    private Car $car;

    protected function setUp(): void
    {
        $insurance = new Insurance('InsuranceCo', 'POL12345', new \DateTime('2023-01-01'), new \DateTime('2024-01-01'), new \DateTime('2023-01-01'));
        $fitness = new Fitness(new \DateTime('2023-01-01'), new \DateTime('2024-01-01'));
        $roadTax = new RoadTax(new \DateTime('2023-01-01'), new \DateTime('2024-01-01'));

        $this->car = new Car('Toyota', 'Corolla', '1234 AB 56', $insurance, $fitness, $roadTax);
    }

    public function testCarConstructorAndGetters(): void
    {
        $this->assertEquals('Toyota', $this->car->getMake());
        $this->assertEquals('Corolla', $this->car->getModel());
        $this->assertEquals('1234 AB 56', $this->car->getRegistrationNumber());
        $this->assertInstanceOf(Insurance::class, $this->car->getInsurance());
        $this->assertInstanceOf(Fitness::class, $this->car->getFitness());
        $this->assertInstanceOf(RoadTax::class, $this->car->getRoadTax());
    }

    public function testIsFitForRoad(): void
    {
        $this->assertFalse($this->car->isFitForRoad());
    }

    public function testAddServiceHistory(): void
    {
        $serviceHistory = $this->createMock(ServiceHistory::class);
        $serviceHistory->method('getServiceDate')->willReturn(new \DateTime('2023-01-01'));

        $this->car->addServiceHistory($serviceHistory);
        $this->assertCount(1, $this->car->getServiceHistories());
    }

    public function testAddServiceHistoryWithFutureDateThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot add service history with a future date.');

        $serviceHistory = $this->createMock(ServiceHistory::class);
        $serviceHistory->method('getServiceDate')->willReturn(new \DateTime('+1 day'));

        $this->car->addServiceHistory($serviceHistory);
    }

    public function testRemoveServiceHistory(): void
    {
        $serviceHistory = $this->createMock(ServiceHistory::class);
        $serviceHistory->method('getServiceDate')->willReturn(new \DateTime('2023-01-01'));

        $this->car->addServiceHistory($serviceHistory);
        $this->assertCount(1, $this->car->getServiceHistories());

        $this->car->removeServiceHistory($serviceHistory);
        $this->assertCount(0, $this->car->getServiceHistories());
    }

    public function testUpdateDetails(): void
    {
        $this->car->updateDetails('Honda', 'Civic');
        $this->assertEquals('Honda', $this->car->getMake());
        $this->assertEquals('Civic', $this->car->getModel());
    }

    public function testListServiceHistories(): void
    {
        $serviceHistory = $this->createMock(ServiceHistory::class);
        $serviceHistory->method('getDescription')->willReturn('Oil change');
        $serviceHistory->method('getServiceDate')->willReturn(new \DateTime('2023-01-01'));

        $this->car->addServiceHistory($serviceHistory);
        $histories = $this->car->listServiceHistories();

        $this->assertCount(1, $histories);
        $this->assertEquals('Oil change', $histories[0]['description']);
        $this->assertEquals('2023-01-01', $histories[0]['serviceDate']);
    }
}
