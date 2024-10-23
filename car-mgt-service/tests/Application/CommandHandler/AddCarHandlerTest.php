<?php

namespace App\Tests\Application\CommandHandler;

use App\Application\Command\AddCarCommand;
use App\Application\CommandHandler\AddCarHandler;
use App\Application\DTO\CarDTO;
use App\Application\DTO\FitnessDTO;
use App\Application\DTO\InsuranceDTO;
use App\Application\DTO\RoadTaxDTO;
use App\Application\Service\LoggerService;
use App\Domain\Car\Car;
use App\Domain\Car\CarRepositoryInterface;
use App\Domain\Car\ValueObject\Fitness;
use App\Domain\Car\ValueObject\Insurance;
use App\Domain\Car\ValueObject\RoadTax;
use Doctrine\ORM\EntityNotFoundException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddCarHandlerTest extends TestCase
{
    private CarRepositoryInterface $carRepository;
    private ValidatorInterface $validator;
    private LoggerService $loggerService;
    private AddCarHandler $addCarHandler;
    private Insurance $insuranceMock;
    private Fitness $fitnessMock;
    private RoadTax $roadTexMock;

    private InsuranceDTO $insuranceDTOMock;
    private FitnessDTO $fitnessDTOMock;
    private RoadTaxDTO $roadTexDTOMock;


    protected function setUp(): void
    {
        $this->carRepository = $this->createMock(CarRepositoryInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->loggerService = $this->createMock(LoggerService::class);
        $this->addCarHandler = new AddCarHandler($this->carRepository, $this->validator, $this->loggerService);
        $this->insuranceMock = $this->createMock(Insurance::class);
        $this->fitnessMock = $this->createMock(Fitness::class);
        $this->roadTexMock = $this->createMock(RoadTax::class);

        $this->insuranceDTOMock = $this->createMock(InsuranceDTO::class);
        $this->fitnessDTOMock = $this->createMock(FitnessDTO::class);
        $this->roadTexDTOMock = $this->createMock(RoadTaxDTO::class);

    }

    public function testHandleSuccessfullyAddsCar(): void
    {
        $carDTO = new CarDTO('Toyota', 'Corolla', '1234 AB 56', 
            new InsuranceDTO('InsuranceCo', 'INS123', '2023-01-01', '2024-01-01', '2023-01-01'),
            new FitnessDTO('2023-01-01', '2024-01-01'),
            new RoadTaxDTO('2023-01-01', '2024-01-01')
        );

        $command = new AddCarCommand($carDTO);

        $car = new Car('Toyota', 'Corolla', '1234 AB 56', 
            new Insurance('InsuranceCo', 'INS123', new \DateTime('2023-01-01'), new \DateTime('2024-01-01'), new \DateTime('2023-01-01')),
            new Fitness(new \DateTime('2023-01-01'), new \DateTime('2024-01-01')),
            new RoadTax(new \DateTime('2023-01-01'), new \DateTime('2024-01-01'))
        );

        $this->carRepository->method('findByRegistrationNumber')->willReturn(null); 
        $this->carRepository->method('save')->willReturnCallback(function (Car $car) {
            $reflectedCar = new \ReflectionClass($car);
            $idProperty = $reflectedCar->getProperty('id'); 
            $idProperty->setAccessible(true);
            $idProperty->setValue($car, 1);

            return $car;
        });
        
        $carId = $this->addCarHandler->handle($command);

        $this->assertEquals(1, $carId, "Expected car ID to be 1 after saving.");
    }

    public function testHandleThrowsDomainExceptionForDuplicateRegistration(): void
    {
        $carDTO = new CarDTO('Toyota', 'Corolla', '1234 AB 56', 
            new InsuranceDTO('InsuranceCo', 'INS123', '2023-01-01', '2024-01-01', '2023-01-01'),
            new FitnessDTO('2023-01-01', '2024-01-01'),
            new RoadTaxDTO('2023-01-01', '2024-01-01')
        );

        $command = new AddCarCommand($carDTO);

        $existingCar = new Car('Toyota', 'Corolla', '1234 AB 56', 
            new Insurance('InsuranceCo', 'INS123', new \DateTime('2023-01-01'), new \DateTime('2024-01-01'), new \DateTime('2023-01-01')),
            new Fitness(new \DateTime('2023-01-01'), new \DateTime('2024-01-01')),
            new RoadTax(new \DateTime('2023-01-01'), new \DateTime('2024-01-01'))
        );

        $this->carRepository->method('findByRegistrationNumber')->willReturn($existingCar);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("An error occurred: A car with this registration number '1234 AB 56' already exists.");

        $this->addCarHandler->handle($command);
    }

    public function testHandleThrowsRuntimeExceptionOnGeneralError(): void
    {
        $carDTO = new CarDTO('Toyota', 'Corolla', '1234 AB 56', 
            new InsuranceDTO('InsuranceCo', 'INS123', '2023-01-01 00:00:00','2024-01-01 00:00:00', '2023-01-01 00:00:00'),
            new FitnessDTO('2023-01-01 00:00:00', '2024-01-01 00:00:00'),
            new RoadTaxDTO('2023-01-01 00:00:00', '2024-01-01 00:00:00')
        );

        $command = new AddCarCommand($carDTO);

        $this->carRepository->method('findByRegistrationNumber')->willReturn(null);

        $this->carRepository->method('save')->will($this->throwException(new \Exception('Database error')));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('An error occurred: Database error');

        $this->addCarHandler->handle($command);
    }
}
