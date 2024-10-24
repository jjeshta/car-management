<?php

namespace App\Tests\Application\QueryHandler;

use App\Application\Query\FindCarsUnfitForRoadQuery;
use App\Application\QueryHandler\FindCarsUnfitForRoadHandler;
use App\Application\Service\LoggerService;
use App\Domain\Car\CarRepositoryInterface;
use PHPUnit\Framework\TestCase;

class FindCarsUnfitForRoadHandlerTest extends TestCase
{
    private $carRepository;
    private $loggerService;
    private $findCarsUnfitForRoadHandler;

    protected function setUp(): void
    {
        $this->carRepository = $this->createMock(CarRepositoryInterface::class);
        $this->loggerService = $this->createMock(LoggerService::class);

        $this->findCarsUnfitForRoadHandler = new FindCarsUnfitForRoadHandler(
            $this->carRepository,
            $this->loggerService
        );
    }

    public function testHandleSuccessfullyFindsCarsUnfitForRoad(): void
    {
        $query = $this->createMock(FindCarsUnfitForRoadQuery::class);

        $unfitCars = ['Car 1', 'Car 2', 'Car 3'];

        $logCall = 0;
        $this->loggerService->expects($this->exactly(2))
            ->method('logInfo')
            ->willReturnCallback(function ($message) use (&$logCall, $unfitCars) {
                $logCall++;
                if ($logCall === 1) {
                    $this->assertEquals('Attempting to find cars unfit for road.', $message);
                } elseif ($logCall === 2) {
                    $this->assertEquals('Successfully retrieved ' . count($unfitCars) . ' cars unfit for road.', $message);
                }
            });

        $this->carRepository->expects($this->once())
            ->method('findCarsUnfitForRoad')
            ->willReturn($unfitCars);

        $result = $this->findCarsUnfitForRoadHandler->handle($query);

        $this->assertSame($unfitCars, $result);
    }

    public function testHandleThrowsRuntimeExceptionOnError(): void
    {
        $query = $this->createMock(FindCarsUnfitForRoadQuery::class);

        $this->loggerService->expects($this->once())
            ->method('logError')
            ->with('An error occurred while finding unfit cars for road: Database error');

        $this->carRepository->expects($this->once())
            ->method('findCarsUnfitForRoad')
            ->willThrowException(new \RuntimeException('Database error'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('An error occurred while finding unfit cars for road: Database error');

        $this->findCarsUnfitForRoadHandler->handle($query);
    }
}
