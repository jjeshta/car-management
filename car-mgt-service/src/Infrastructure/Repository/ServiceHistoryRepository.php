<?php

namespace App\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;
use App\Domain\ServiceHistory\ServiceHistory;
use App\Domain\ServiceHistory\ServiceHistoryRepositoryInterface;

class ServiceHistoryRepository implements ServiceHistoryRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {}

    public function save(ServiceHistory $serviceHistory): void
    {
        $this->entityManager->persist($serviceHistory);
        $this->entityManager->flush();
    }

    public function remove(ServiceHistory $serviceHistory): void
    {
        $this->entityManager->remove($serviceHistory);
        $this->entityManager->flush();
    }

    public function find(int $id): ?ServiceHistory
    {
        return $this->entityManager->getRepository(ServiceHistory::class)->find($id);
    }
}
