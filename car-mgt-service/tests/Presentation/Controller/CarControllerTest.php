<?php

namespace App\Tests\Presentation\Controller;

use App\Application\Command\AddCarCommand;
use App\Application\Command\RemoveCarCommand;
use App\Application\Command\UpdateCarCommand;
use App\Application\CommandHandler\AddCarHandler;
use App\Application\CommandHandler\RemoveCarHandler;
use App\Application\CommandHandler\UpdateCarHandler;
use App\Application\DTO\CarDTO;
use App\Application\DTO\FitnessDTO;
use App\Application\DTO\InsuranceDTO;
use App\Application\DTO\RoadTaxDTO;
use App\Application\Query\FindCarQuery;
use App\Application\Query\FindCarsFitForRoadQuery;
use App\Application\Query\FindCarsUnfitForRoadQuery;
use App\Application\QueryHandler\FindCarHandler;
use App\Application\QueryHandler\FindCarsFitForRoadHandler;
use App\Application\QueryHandler\FindCarsUnfitForRoadHandler;
use App\Presentation\Controller\CarController;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CarControllerTest extends TestCase
{
    private $serializer;
    private $validator;
    private $addHandler;
    private $findHandler;
    private $findCarsFitHandler;
    private $findCarsUnfitHandler;
    private $updateHandler;
    private $removeHandler;

    private $container;
    private CarController $controller;

    protected function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->addHandler = $this->createMock(AddCarHandler::class);
        $this->findHandler = $this->createMock(FindCarHandler::class);
        $this->findCarsFitHandler = $this->createMock(FindCarsFitForRoadHandler::class);
        $this->findCarsUnfitHandler = $this->createMock(FindCarsUnfitForRoadHandler::class);
        $this->updateHandler = $this->createMock(UpdateCarHandler::class);
        $this->removeHandler = $this->createMock(RemoveCarHandler::class);

        $this->container = $this->createMock(ContainerInterface::class);
        $this->controller = new CarController($this->container);
    }

    private function createValidCarDTO(): CarDTO
    {
        return new CarDTO(
            'Toyota',
            'Corolla',
            '1234 AB 56',
            new InsuranceDTO('InsuranceCo', 'INS123', '2023-01-01 00:00:00', '2024-01-01 00:00:00', '2023-01-01 00:00:00'),
            new FitnessDTO('2023-01-01 00:00:00', '2024-01-01 00:00:00'),
            new RoadTaxDTO('2023-01-01 00:00:00', '2024-01-01 00:00:00')
        );
    }

    private function createInvalidCarDTO(): CarDTO
    {
        return new CarDTO(
            '',
            '',
            '6597',
            $this->createInvalidInsuranceDTO(),
            $this->createInvalidFitnessDTO(),
            $this->createInvalidRoadTaxDTO()
        );
    }

    private function createInvalidInsuranceDTO(): InsuranceDTO
    {
        return new InsuranceDTO(
            '',
            '',
            '',
            '',
            ''
        );
    }

    private function createInvalidFitnessDTO(): FitnessDTO
    {
        return new FitnessDTO(
            '',
            ''
        );
    }

    private function createInvalidRoadTaxDTO(): RoadTaxDTO
    {
        return new RoadTaxDTO(
            '',
            ''
        );
    }

    public function testCreateCarSuccessfully(): void
    {
        $carDTO = $this->createValidCarDTO();
        $request = new Request([], [], [], [], [], [], json_encode($carDTO));

        $this->serializer->method('deserialize')->willReturn($carDTO);
        $this->validator->method('validate')->willReturn(new ConstraintViolationList());
        $this->addHandler->expects($this->once())
            ->method('handle')
            ->with(new AddCarCommand($carDTO))
            ->willReturn(1);

        $response = $this->controller->createCar($request, $this->serializer, $this->validator, $this->addHandler);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Car added successfully.', 'car_id' => 1]),
            $response->getContent()
        );
    }

    public function testCreateCarWithInvalidData(): void
    {
        $carDTO = $this->createInvalidCarDTO();

        $violationsArray = [
            new ConstraintViolation('Make should not be blank.', null, [], $carDTO, 'make', ''),
            new ConstraintViolation('Model should not be blank.', null, [], $carDTO, 'model', ''),
            new ConstraintViolation("Registration number must follow the pattern '1234 AB 56'.", null, [], $carDTO, 'registrationNumber', ''),

            new ConstraintViolation("This value is not a valid datetime for 'dateIssued'. The correct format is Y-m-d H:i:s.", null, [], $carDTO, 'insurance.dateIssued', ''),
            new ConstraintViolation('Insurer should not be blank.', null, [], $carDTO, 'insurance.insurer', ''),
            new ConstraintViolation('Policy number should not be blank.', null, [], $carDTO, 'insurance.policyNumber', ''),
            new ConstraintViolation('Date issued should not be blank.', null, [], $carDTO, 'insurance.dateIssued', ''),
            new ConstraintViolation('Date issued must be less than date expiry.', null, [], $carDTO, 'insurance.dateIssued', ''),
            new ConstraintViolation('Date expiry should not be blank.', null, [], $carDTO, 'insurance.dateExpiry', ''),
            new ConstraintViolation('Date expiry must be later than date issued.', null, [], $carDTO, 'insurance.dateExpiry', ''),
            new ConstraintViolation('Date start should not be blank.', null, [], $carDTO, 'insurance.dateStart', ''),
            new ConstraintViolation('Date start cannot be equal to date expiry.', null, [], $carDTO, 'insurance.dateStart', ''),
            new ConstraintViolation('Date start cannot be greater than date expiry.', null, [], $carDTO, 'insurance.dateStart', ''),

            new ConstraintViolation("This value is not a valid datetime for 'issued'. The correct format is Y-m-d H:i:s.", null, [], $carDTO, 'fitness.issued', ''),
            new ConstraintViolation('Issued date should not be blank.', null, [], $carDTO, 'fitness.issued', ''),
            new ConstraintViolation('Valid until date should not be blank.', null, [], $carDTO, 'fitness.validUntil', ''),
            new ConstraintViolation('Valid until date must be later than the issued date.', null, [], $carDTO, 'fitness.validUntil', ''),

            new ConstraintViolation("This value is not a valid datetime for 'issued'. The correct format is Y-m-d H:i:s.", null, [], $carDTO, 'roadTax.issued', ''),
            new ConstraintViolation('Issued date should not be blank.', null, [], $carDTO, 'roadTax.issued', ''),
            new ConstraintViolation('Valid until date should not be blank.', null, [], $carDTO, 'roadTax.validUntil', ''),
            new ConstraintViolation('Valid until date must be later than the issued date.', null, [], $carDTO, 'roadTax.validUntil', ''),
        ];

        $violations = new ConstraintViolationList($violationsArray);

        $this->serializer->method('deserialize')->willReturn($carDTO);
        $this->validator->method('validate')->willReturn($violations);

        $request = new Request([], [], [], [], [], [], json_encode($carDTO));

        $response = $this->controller->createCar($request, $this->serializer, $this->validator, $this->addHandler);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'status' => 'error',
                'errors' => [
                    'make: Make should not be blank.',
                    'model: Model should not be blank.',
                    'registrationNumber: Registration number must follow the pattern \'1234 AB 56\'.',
                    'insurance.dateIssued: This value is not a valid datetime for \'dateIssued\'. The correct format is Y-m-d H:i:s.',
                    'insurance.insurer: Insurer should not be blank.',
                    'insurance.policyNumber: Policy number should not be blank.',
                    'insurance.dateIssued: Date issued should not be blank.',
                    'insurance.dateIssued: Date issued must be less than date expiry.',
                    'insurance.dateExpiry: Date expiry should not be blank.',
                    'insurance.dateExpiry: Date expiry must be later than date issued.',
                    'insurance.dateStart: Date start should not be blank.',
                    'insurance.dateStart: Date start cannot be equal to date expiry.',
                    'insurance.dateStart: Date start cannot be greater than date expiry.',
                    'fitness.issued: This value is not a valid datetime for \'issued\'. The correct format is Y-m-d H:i:s.',
                    'fitness.issued: Issued date should not be blank.',
                    'fitness.validUntil: Valid until date should not be blank.',
                    'fitness.validUntil: Valid until date must be later than the issued date.',
                    'roadTax.issued: This value is not a valid datetime for \'issued\'. The correct format is Y-m-d H:i:s.',
                    'roadTax.issued: Issued date should not be blank.',
                    'roadTax.validUntil: Valid until date should not be blank.',
                    'roadTax.validUntil: Valid until date must be later than the issued date.'
                ]
            ]),
            $response->getContent()
        );
    }

    public function testCreateCarThrowsException(): void
{
    $carDTO = new CarDTO(
        'Toyota',
        'Corolla',
        '1234 AB 56',
        new InsuranceDTO('InsuranceCo', 'INS123', '2023-01-01 00:00:00', '2024-01-01 00:00:00', '2023-01-01 00:00:00'),
        new FitnessDTO('2023-01-01 00:00:00', '2024-01-01 00:00:00'),
        new RoadTaxDTO('2023-01-01 00:00:00', '2024-01-01 00:00:00')
    );
    $request = new Request([], [], [], [], [], [], json_encode($carDTO));

    $this->serializer->method('deserialize')->willReturn($carDTO);
    $this->validator->method('validate')->willReturn(new ConstraintViolationList());
    $this->addHandler->method('handle')->willThrowException(new \Exception('An unexpected error occurred.'));

    $container = $this->createMock(ContainerInterface::class);
    $controller = new CarController($container);

    $response = $controller->createCar($request, $this->serializer, $this->validator, $this->addHandler);

    $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    $this->assertJsonStringEqualsJsonString(
        json_encode([
            'status' => 'error',
            'message' => 'An unexpected error occurred.'
        ]),
        $response->getContent()
    );
}
    public function testFindCarSuccessfully(): void
    {
        $registrationNumber = '1234 AB 56';
        $query = new FindCarQuery($registrationNumber);
        $carData = [
            'make' => 'Toyota',
            'model' => 'Corolla',
            'registrationNumber' => $registrationNumber
        ];

        $this->validator->method('validate')->with($query)->willReturn(new ConstraintViolationList());
        $this->findHandler->method('handle')->with($query)->willReturn($carData);

        $response = $this->controller->findCar($registrationNumber, $this->validator, $this->findHandler);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode($carData), $response->getContent());
    }

    public function testFindCarValidationError(): void
    {
        $registrationNumber = 'invalid';
        $query = new FindCarQuery($registrationNumber);
        
        $violation = new ConstraintViolation(
            'Registration number is invalid.', null, [], '', 'registrationNumber', $registrationNumber
        );
        $violations = new ConstraintViolationList([$violation]);

        $this->validator->method('validate')->with($query)->willReturn($violations);

        $response = $this->controller->findCar($registrationNumber, $this->validator, $this->findHandler);

        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['status' => 'error', 'errors' => ['registrationNumber: Registration number is invalid.']]),
            $response->getContent()
        );
    }

    public function testFindCarNotFound(): void
    {
        $registrationNumber = '1234 AB 56';
        $query = new FindCarQuery($registrationNumber);

        $this->validator->method('validate')->with($query)->willReturn(new ConstraintViolationList());
        $this->findHandler->method('handle')->with($query)->willReturn(null);

        $response = $this->controller->findCar($registrationNumber, $this->validator, $this->findHandler);

        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['status' => 'error', 'message' => 'Car not found']),
            $response->getContent()
        );
    }

    public function testFindCarException(): void
    {
        $registrationNumber = '1234 AB 56';
        $query = new FindCarQuery($registrationNumber);

        $this->validator->method('validate')->with($query)->willReturn(new ConstraintViolationList());
        $this->findHandler->method('handle')->willThrowException(new \RuntimeException('An unexpected error occurred'));

        $response = $this->controller->findCar($registrationNumber, $this->validator, $this->findHandler);

        $this->assertEquals(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'status' => 'error',
                'message' => 'An error occurred while retrieving the car: An unexpected error occurred'
            ]),
            $response->getContent()
        );
    }

    public function testFindCarsFitForRoadSuccessfully(): void
    {
        $query = new FindCarsFitForRoadQuery();
        $carsData = [
            [
                'make' => 'Toyota',
                'model' => 'Corolla',
                'registrationNumber' => '1234 AB 56',
            ],
            [
                'make' => 'Honda',
                'model' => 'Civic',
                'registrationNumber' => '5678 CD 90',
            ],
        ];

        $this->findCarsFitHandler->method('handle')->with($query)->willReturn($carsData);

        $response = $this->controller->findCarsFitForRoad($this->findCarsFitHandler);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode($carsData), $response->getContent());
    }

    public function testFindCarsFitForRoadException(): void
    {
        $query = new FindCarsFitForRoadQuery();

        $this->findCarsFitHandler->method('handle')->willThrowException(new \RuntimeException('Unexpected error'));

        $response = $this->controller->findCarsFitForRoad($this->findCarsFitHandler);

        $this->assertEquals(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'status' => 'error',
                'message' => 'An error occurred while retrieving cars fit for road: Unexpected error'
            ]),
            $response->getContent()
        );
    }

    public function testFindCarsUnfitForRoadSuccessfully(): void
    {
        $query = new FindCarsUnfitForRoadQuery();
        $carsData = [
            [
                'make' => 'Ford',
                'model' => 'Focus',
                'registrationNumber' => '7890 EF 12',
            ],
        ];

        $this->findCarsUnfitHandler->method('handle')->with($query)->willReturn($carsData);

        $response = $this->controller->findCarsUnfitForRoad($this->findCarsUnfitHandler);

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode($carsData), $response->getContent());
    }

    public function testFindCarsUnfitForRoadException(): void
    {
        $query = new FindCarsUnfitForRoadQuery();

        $this->findCarsUnfitHandler->method('handle')->willThrowException(new \RuntimeException('Unexpected error'));

        $response = $this->controller->findCarsUnfitForRoad($this->findCarsUnfitHandler);

        $this->assertEquals(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'status' => 'error',
                'message' => 'An error occurred while retrieving cars unfit for road: Unexpected error'
            ]),
            $response->getContent()
        );
    }

    public function testUpdateCarSuccessfully(): void
    {
        $registrationNumber = '1234 AB 56';
        $requestData = [
            'make' => 'Toyota',
            'model' => 'Corolla'
        ];
        $request = new Request([], [], [], [], [], [], json_encode($requestData));

        $command = new UpdateCarCommand($registrationNumber, $requestData['make'], $requestData['model']);
        $this->updateHandler->expects($this->once())->method('handle')->with($command);

        $response = $this->controller->updateCar($registrationNumber, $request, $this->updateHandler);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Car updated successfully.']),
            $response->getContent()
        );
    }

    public function testUpdateCarNotFound(): void
    {
        $registrationNumber = '9999 ZZ 99';
        $requestData = [
            'make' => 'Nissan',
            'model' => 'Altima'
        ];
        $request = new Request([], [], [], [], [], [], json_encode($requestData));

        $command = new UpdateCarCommand($registrationNumber, $requestData['make'], $requestData['model']);
        $this->updateHandler->method('handle')->willThrowException(new \InvalidArgumentException('Car not found'));

        $response = $this->controller->updateCar($registrationNumber, $request, $this->updateHandler);

        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['status' => 'error', 'message' => 'Car not found']),
            $response->getContent()
        );
    }

    public function testUpdateCarException(): void
    {
        $registrationNumber = '1234 AB 56';
        $requestData = [
            'make' => 'Ford',
            'model' => 'Fusion'
        ];
        $request = new Request([], [], [], [], [], [], json_encode($requestData));

        $command = new UpdateCarCommand($registrationNumber, $requestData['make'], $requestData['model']);
        $this->updateHandler->method('handle')->willThrowException(new \RuntimeException('Database error'));

        $response = $this->controller->updateCar($registrationNumber, $request, $this->updateHandler);

        $this->assertEquals(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['status' => 'error', 'message' => 'Database error']),
            $response->getContent()
        );
    }
    public function testDeleteCarSuccessfully(): void
    {
        $registrationNumber = '1234 AB 56';
        $command = new RemoveCarCommand($registrationNumber);

        $this->removeHandler->expects($this->once())->method('handle')->with($command);

        $response = $this->controller->deleteCar($registrationNumber, $this->removeHandler);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Car removed successfully.']),
            $response->getContent()
        );
    }

    public function testDeleteCarNotFound(): void
    {
        $registrationNumber = '9999 ZZ 99';
        $command = new RemoveCarCommand($registrationNumber);

        $this->removeHandler->method('handle')->willThrowException(new \InvalidArgumentException('Car not found'));

        $response = $this->controller->deleteCar($registrationNumber, $this->removeHandler);

        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['status' => 'error', 'message' => 'Car not found']),
            $response->getContent()
        );
    }

    public function testDeleteCarException(): void
    {
        $registrationNumber = '1234 AB 56';
        $command = new RemoveCarCommand($registrationNumber);

        $this->removeHandler->method('handle')->willThrowException(new \RuntimeException('Database error'));

        $response = $this->controller->deleteCar($registrationNumber, $this->removeHandler);

        $this->assertEquals(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['status' => 'error', 'message' => 'Database error']),
            $response->getContent()
        );
    }
}

