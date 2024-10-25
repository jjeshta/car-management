<?php

namespace App\Tests\Presentation\Controller;

use App\Presentation\Controller\ServiceHistoryController;
use App\Application\Command\AddServiceHistoryCommand;
use App\Application\Command\RemoveServiceHistoryCommand;
use App\Application\CommandHandler\AddServiceHistoryHandler;
use App\Application\CommandHandler\RemoveServiceHistoryHandler;
use App\Application\DTO\ServiceHistoryDTO;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;

class ServiceHistoryControllerTest extends TestCase
{
    private $serializer;
    private $validator;
    private $addHandler;
    private $removeHandler;
    private $controller;

    protected function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->addHandler = $this->createMock(AddServiceHistoryHandler::class);
        $this->removeHandler = $this->createMock(RemoveServiceHistoryHandler::class);
        $this->controller = new ServiceHistoryController();
    }

    public function testAddServiceHistorySuccessfully(): void
    {
        $jsonData = json_encode([
            'carRegistrationNumber' => '1234 AB 56',
            'date' => '2023-10-15 10:00:00',
            'description' => 'Service for brakes and oil change'
        ]);
        $request = new Request([], [], [], [], [], [], $jsonData);

        $serviceHistoryDTO = new ServiceHistoryDTO('1234 AB 56', '2023-10-15 10:00:00', 'Service for brakes and oil change');

        $this->serializer->method('deserialize')
            ->with($request->getContent(), ServiceHistoryDTO::class, 'json')
            ->willReturn($serviceHistoryDTO);

        $this->validator->method('validate')
            ->with($serviceHistoryDTO)
            ->willReturn(new ConstraintViolationList());

        $this->addHandler->expects($this->once())
            ->method('handle')
            ->with(new AddServiceHistoryCommand($serviceHistoryDTO));

        $response = $this->controller->add($request, $this->serializer, $this->validator, $this->addHandler);
        $this->assertEquals(JsonResponse::HTTP_CREATED, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => 'Service history added successfully.']),
            $response->getContent()
        );
    }

    public function testAddServiceHistoryValidationError(): void
    {
        $jsonData = json_encode([
            'carRegistrationNumber' => '1234 AB 56',
            'date' => '2023-10-15 10:00:00',
            'description' => 'Short' // Short description to trigger validation error
        ]);
        $request = new Request([], [], [], [], [], [], $jsonData);
    
        $serviceHistoryDTO = new ServiceHistoryDTO('1234 AB 56', '2023-10-15 10:00:00', 'Short');
    
        $this->serializer->method('deserialize')
            ->with($request->getContent(), ServiceHistoryDTO::class, 'json')
            ->willReturn($serviceHistoryDTO);
    
        $violation = new ConstraintViolation(
            'Description must be at least 10 characters long.', // message
            null, // message template
            [],   // message parameters
            $serviceHistoryDTO, // root object
            'description', // property path
            'Short' // invalid value
        );
    
        $violations = new ConstraintViolationList([$violation]);
    
        $this->validator->method('validate')
            ->with($serviceHistoryDTO)
            ->willReturn($violations);
    
        $response = $this->controller->add($request, $this->serializer, $this->validator, $this->addHandler);
    
        $this->assertEquals(JsonResponse::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertStringContainsString('error', $response->getContent());
    }
    

    public function testAddServiceHistoryException(): void
    {
        $jsonData = json_encode([
            'carRegistrationNumber' => '1234 AB 56',
            'date' => '2023-10-15 10:00:00',
            'description' => 'Service for brakes and oil change'
        ]);
        $request = new Request([], [], [], [], [], [], $jsonData);

        $serviceHistoryDTO = new ServiceHistoryDTO('1234 AB 56', '2023-10-15 10:00:00', 'Service for brakes and oil change');

        $this->serializer->method('deserialize')
            ->willReturn($serviceHistoryDTO);

        $this->validator->method('validate')
            ->willReturn(new ConstraintViolationList());

        $this->addHandler->method('handle')
            ->willThrowException(new \Exception('Database error'));

        $response = $this->controller->add($request, $this->serializer, $this->validator, $this->addHandler);
        $this->assertEquals(JsonResponse::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertStringContainsString('An error occurred', $response->getContent());
    }

    public function testDeleteServiceHistorySuccessfully(): void
    {
        $serviceHistoryId = 1;
        $command = new RemoveServiceHistoryCommand($serviceHistoryId);

        $this->removeHandler->expects($this->once())
            ->method('handle')
            ->with($command);

        $response = $this->controller->delete($serviceHistoryId, $this->removeHandler);
        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['status' => 'success', 'message' => 'Service history deleted successfully.']),
            $response->getContent()
        );
    }

    public function testDeleteServiceHistoryNotFound(): void
    {
        $serviceHistoryId = 999;
        $command = new RemoveServiceHistoryCommand($serviceHistoryId);

        $this->removeHandler->method('handle')
            ->willThrowException(new \InvalidArgumentException("Service history not found with ID: {$serviceHistoryId}"));

        $response = $this->controller->delete($serviceHistoryId, $this->removeHandler);
        $this->assertEquals(JsonResponse::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertStringContainsString('Service history not found', $response->getContent());
    }
}
