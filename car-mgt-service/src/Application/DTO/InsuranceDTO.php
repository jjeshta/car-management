<?php

namespace App\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class InsuranceDTO
{
    #[Assert\NotBlank(message: "Insurer should not be blank.")]
    private string $insurer;

    #[Assert\NotBlank(message: "Policy number should not be blank.")]
    private string $policyNumber;

    #[Assert\NotBlank(message: "Date issued should not be blank.")]
    #[Assert\Date]
    private \DateTimeInterface $dateIssued;

    #[Assert\NotBlank(message: "Date expiry should not be blank.")]
    #[Assert\Date]
    private \DateTimeInterface $dateExpiry;

    #[Assert\NotBlank(message: "Date start should not be blank.")]
    #[Assert\Date]
    private \DateTimeInterface $dateStart;

    public function __construct(string $insurer, string $policyNumber, \DateTimeInterface $dateIssued, \DateTimeInterface $dateExpiry, \DateTimeInterface $dateStart)
    {
        $this->insurer = $insurer;
        $this->policyNumber = $policyNumber;
        $this->dateIssued = $dateIssued;
        $this->dateExpiry = $dateExpiry;
        $this->dateStart = $dateStart;
    }

    public function getInsurer(): string
    {
        return $this->insurer;
    }

    public function getPolicyNumber(): string
    {
        return $this->policyNumber;
    }

    public function getDateIssued(): \DateTimeInterface
    {
        return $this->dateIssued;
    }

    public function getDateExpiry(): \DateTimeInterface
    {
        return $this->dateExpiry;
    }

    public function getDateStart(): \DateTimeInterface
    {
        return $this->dateStart;
    }

}
