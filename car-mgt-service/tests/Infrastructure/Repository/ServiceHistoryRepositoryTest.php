<?php

namespace App\Tests\Infrastructure\Repository;

use App\Domain\ServiceHistory\ServiceHistory;
use App\Infrastructure\Repository\ServiceHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class ServiceHistoryRepositoryTest extends TestCase
{
    private $entityManager;
    private $repository;
    private $serviceHistoryRepository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->repository = $this->createMock(EntityRepository::class);
        $this->serviceHistoryRepository = new ServiceHistoryRepository($this->entityManager);
    }

    public function testSaveServiceHistory(): void
    {
        $serviceHistory = $this->createMock(ServiceHistory::class);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($serviceHistory);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->serviceHistoryRepository->save($serviceHistory);
    }

    public function testRemoveServiceHistory(): void
    {
        $serviceHistory = $this->createMock(ServiceHistory::class);

        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($serviceHistory);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->serviceHistoryRepository->remove($serviceHistory);
    }

    public function testFindServiceHistoryById(): void
    {
        $serviceHistory = $this->createMock(ServiceHistory::class);
        $serviceHistoryId = 1;

        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with(ServiceHistory::class)
            ->willReturn($this->repository);

        $this->repository->expects($this->once())
            ->method('find')
            ->with($serviceHistoryId)
            ->willReturn($serviceHistory);

        $result = $this->serviceHistoryRepository->find($serviceHistoryId);

        $this->assertSame($serviceHistory, $result);
    }

    public function testFindServiceHistoryByIdReturnsNull(): void
    {
        $serviceHistoryId = 999;

        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with(ServiceHistory::class)
            ->willReturn($this->repository);

        $this->repository->expects($this->once())
            ->method('find')
            ->with($serviceHistoryId)
            ->willReturn(null);

        $result = $this->serviceHistoryRepository->find($serviceHistoryId);

        $this->assertNull($result);
    }
}
