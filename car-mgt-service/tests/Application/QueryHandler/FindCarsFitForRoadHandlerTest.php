<?php

namespace App\Tests\Application\QueryHandler;

use App\Application\Query\FindCarsFitForRoadQuery;
use App\Application\QueryHandler\FindCarsFitForRoadHandler;
use App\Application\Service\LoggerService;
use App\Domain\Car\CarRepositoryInterface;
use PHPUnit\Framework\TestCase;

class FindCarsFitForRoadHandlerTest extends TestCase
{
    private $carRepository;
    private $loggerService;
    private $findCarsFitForRoadHandler;

    protected function setUp(): void
    {
        $this->carRepository = $this->createMock(CarRepositoryInterface::class);
        $this->loggerService = $this->createMock(LoggerService::class);

        $this->findCarsFitForRoadHandler = new FindCarsFitForRoadHandler(
            $this->carRepository,
            $this->loggerService
        );
    }

    public function testHandleSuccessfullyFindsCarsFitForRoad(): void
    {
        $query = $this->createMock(FindCarsFitForRoadQuery::class);

        $fitCars = ['Car 1', 'Car 2', 'Car 3'];

        $logCall = 0;
        $this->loggerService->expects($this->exactly(2))
            ->method('logInfo')
            ->willReturnCallback(function ($message) use (&$logCall, $fitCars) {
                $logCall++;
                if ($logCall === 1) {
                    $this->assertEquals('Attempting to find cars fit for road.', $message);
                } elseif ($logCall === 2) {
                    $this->assertEquals('Successfully retrieved ' . count($fitCars) . ' cars fit for road.', $message);
                }
            });

        $this->carRepository->expects($this->once())
            ->method('findCarsFitForRoad')
            ->willReturn($fitCars);

        $result = $this->findCarsFitForRoadHandler->handle($query);

        $this->assertSame($fitCars, $result);
    }

    public function testHandleThrowsRuntimeExceptionOnError(): void
    {
        $query = $this->createMock(FindCarsFitForRoadQuery::class);

        $this->loggerService->expects($this->once())
            ->method('logError')
            ->with('An error occurred while finding fit cars for road: Database error');

        $this->carRepository->expects($this->once())
            ->method('findCarsFitForRoad')
            ->willThrowException(new \RuntimeException('Database error'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('An error occurred while finding fit cars for road: Database error');

        $this->findCarsFitForRoadHandler->handle($query);
    }
}
