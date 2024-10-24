<?php

namespace App\Tests\Domain\Car\ValueObject;

use App\Domain\Car\ValueObject\RoadTax;
use PHPUnit\Framework\TestCase;

class RoadTaxTest extends TestCase
{
    public function testRoadTaxConstructorAndGetters(): void
    {
        $issued = new \DateTime('2023-01-01');
        $validUntil = new \DateTime('2024-01-01');

        $roadTax = new RoadTax($issued, $validUntil);

        $this->assertSame($issued, $roadTax->getIssued());
        $this->assertSame($validUntil, $roadTax->getValidUntil());
    }

    public function testIsValidReturnsTrueWhenValidUntilInFuture(): void
    {
        $issued = new \DateTime('2023-01-01');
        $validUntil = new \DateTime('+1 year');

        $roadTax = new RoadTax($issued, $validUntil);

        $this->assertTrue($roadTax->isValid());
    }

    public function testIsValidReturnsFalseWhenValidUntilInPast(): void
    {
        $issued = new \DateTime('2023-01-01');
        $validUntil = new \DateTime('-1 day');

        $roadTax = new RoadTax($issued, $validUntil);

        $this->assertFalse($roadTax->isValid());
    }
}
