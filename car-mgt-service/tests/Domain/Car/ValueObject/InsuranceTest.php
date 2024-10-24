<?php

namespace App\Tests\Domain\Car\ValueObject;

use App\Domain\Car\ValueObject\Insurance;
use PHPUnit\Framework\TestCase;

class InsuranceTest extends TestCase
{
    public function testInsuranceConstructorAndGetters(): void
    {
        $insurer = 'InsuranceCo';
        $policyNumber = 'POL12345';
        $dateIssued = new \DateTime('2023-01-01');
        $dateExpiry = new \DateTime('2024-01-01');
        $dateStart = new \DateTime('2023-01-01');

        $insurance = new Insurance($insurer, $policyNumber, $dateIssued, $dateExpiry, $dateStart);

        $this->assertSame($insurer, $insurance->getInsurer());
        $this->assertSame($policyNumber, $insurance->getPolicyNumber());
        $this->assertSame($dateIssued, $insurance->getDateIssued());
        $this->assertSame($dateExpiry, $insurance->getDateExpiry());
        $this->assertSame($dateStart, $insurance->getDateStart());
    }

    public function testIsValidReturnsTrueWhenDateExpiryInFuture(): void
    {
        $insurer = 'InsuranceCo';
        $policyNumber = 'POL12345';
        $dateIssued = new \DateTime('2023-01-01');
        $dateExpiry = new \DateTime('+1 year');
        $dateStart = new \DateTime('2023-01-01');

        $insurance = new Insurance($insurer, $policyNumber, $dateIssued, $dateExpiry, $dateStart);

        $this->assertTrue($insurance->isValid());
    }

    public function testIsValidReturnsFalseWhenDateExpiryInPast(): void
    {
        $insurer = 'InsuranceCo';
        $policyNumber = 'POL12345';
        $dateIssued = new \DateTime('2023-01-01');
        $dateExpiry = new \DateTime('-1 day');
        $dateStart = new \DateTime('2023-01-01');

        $insurance = new Insurance($insurer, $policyNumber, $dateIssued, $dateExpiry, $dateStart);

        $this->assertFalse($insurance->isValid());
    }
}
