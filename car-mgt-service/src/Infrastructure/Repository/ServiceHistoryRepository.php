<?php

namespace App\Infrastructure\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use App\Domain\Car\Car;
use App\Domain\ServiceHistory\ServiceHistory;
use App\Domain\ServiceHistory\ServiceHistoryRepositoryInterface;

class ServiceHistoryRepository implements ServiceHistoryRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(ServiceHistory::class);
    }

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

    public function findByCar(Car $car): array
    {
        return $this->repository->findBy(['car' => $car]);
    }

    public function find(int $id): ?ServiceHistory
    {
        return $this->repository->find($id);
    }
}
