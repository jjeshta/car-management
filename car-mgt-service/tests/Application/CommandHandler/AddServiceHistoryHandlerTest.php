<?php
namespace App\Tests\Application\CommandHandler;

use App\Application\Command\AddServiceHistoryCommand;
use App\Application\CommandHandler\AddServiceHistoryHandler;
use App\Application\Service\LoggerService;
use App\Domain\ServiceHistory\ServiceHistory;
use App\Domain\ServiceHistory\ServiceHistoryRepositoryInterface;
use App\Domain\Car\Car;
use App\Domain\Car\CarRepositoryInterface;
use App\Application\DTO\ServiceHistoryDTO;
use App\Domain\Car\ValueObject\Fitness;
use App\Domain\Car\ValueObject\Insurance;
use App\Domain\Car\ValueObject\RoadTax;
use PHPUnit\Framework\TestCase;

class AddServiceHistoryHandlerTest extends TestCase
{
    private AddServiceHistoryHandler $handler;
    private $serviceHistoryRepository;
    private $carRepository;
    private $loggerService;
    private Insurance $insuranceMock;
    private Fitness $fitnessMock;
    private RoadTax $roadTaxMock;

    protected function setUp(): void
    {
        $this->serviceHistoryRepository = $this->createMock(ServiceHistoryRepositoryInterface::class);
        $this->carRepository = $this->createMock(CarRepositoryInterface::class);
        $this->loggerService = $this->createMock(LoggerService::class);
        
        $this->handler = new AddServiceHistoryHandler(
            $this->serviceHistoryRepository,
            $this->carRepository,
            $this->loggerService
        );

        $this->insuranceMock = $this->createMock(Insurance::class);
        $this->fitnessMock = $this->createMock(Fitness::class);
        $this->roadTaxMock = $this->createMock(RoadTax::class);
    }

    // public function testHandleAddsServiceHistorySuccessfully(): void
    // {
    //     $carRegistrationNumber = '1234 AB 56';
    //     $serviceHistoryDTO = new ServiceHistoryDTO(
    //         $carRegistrationNumber,
    //         'Oil Change',
    //         '2023-01-01 10:00:00'
    //     );
    //     $command = new AddServiceHistoryCommand($serviceHistoryDTO);

    //     $car = new Car('Toyota', 'Corolla', $carRegistrationNumber, $this->insuranceMock, $this->fitnessMock , $this->roadTaxMock);

    //     $this->carRepository->method('findByRegistrationNumber')
    //         ->willReturn($car);

    //     $this->loggerService->expects($this->exactly(3))
    //         ->method('logInfo')
    //         ->withConsecutive(
    //             ["Handling AddServiceHistoryCommand for car registration number: '{$carRegistrationNumber}'"],
    //             ["Car with registration number '{$carRegistrationNumber}' found. Proceeding to add service history."],
    //             ["Service history added successfully for car registration number: '{$carRegistrationNumber}'."]
    //         );

    //     $this->serviceHistoryRepository->expects($this->once())
    //         ->method('save')
    //         ->with($this->callback(function (ServiceHistory $serviceHistory) use ($serviceHistoryDTO, $car) {
    //             return $serviceHistory->getDescription() === $serviceHistoryDTO->getDescription() &&
    //                 $serviceHistory->getDate() == (new \DateTime($serviceHistoryDTO->getDate())) &&
    //                 $serviceHistory->getCar() === $car;
    //         }));

    //     $this->handler->handle($command);

    // }
}
