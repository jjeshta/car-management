<?php

namespace App\Domain\Car\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class Insurance 
{
    #[ORM\Column(type: "string", length: 100)]
    #[Assert\NotBlank]
    private string $insurer;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\NotBlank]
    private string $policyNumber;

    #[ORM\Column(type: "date")]
    #[Assert\NotBlank]
    private \DateTimeInterface $dateIssued;

    #[ORM\Column(type: "date")]
    #[Assert\NotBlank]
    private \DateTimeInterface $dateExpiry;

    #[ORM\Column(type: "date")]
    #[Assert\NotBlank]
    private \DateTimeInterface $dateStart;

    public function __construct(string $insurer, string $policyNumber, \DateTime $dateIssued, \DateTime $dateExpiry, \DateTime $dateStart)
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

    public function isValid(): bool
    {
        return new \DateTime() <= $this->dateExpiry;
    }

}