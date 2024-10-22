<?php

namespace App\Presentation\Controller;

use App\Application\Command\AddCarCommand;
use App\Application\Command\RemoveCarCommand;
use App\Application\Command\UpdateCarCommand;
use App\Application\CommandHandler\AddCarHandler;
use App\Application\CommandHandler\RemoveCarHandler;
use App\Application\CommandHandler\UpdateCarHandler;
use App\Application\DTO\CarDTO;
use App\Application\Query\FindCarQuery;
use App\Application\Query\FindCarsFitForRoadQuery;
use App\Application\Query\FindCarsUnfitForRoadQuery;
use App\Application\QueryHandler\FindCarHandler;
use App\Application\QueryHandler\FindCarsFitForRoadHandler;
use App\Application\QueryHandler\FindCarsUnfitForRoadHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

class CarController extends AbstractController
{

    #[Route('/api/car', methods: ['POST'])]
    public function createCar(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, AddCarHandler $handler): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $carDTO = $serializer->deserialize($request->getContent(), CarDTO::class, 'json');

            $errors = $validator->validate($carDTO);
            if (count($errors) > 0) {
                return new JsonResponse([
                    'status' => 'error',
                    'errors' => (string) $errors,
                ], Response::HTTP_BAD_REQUEST);
            }

            $command = new AddCarCommand($carDTO);
            $carID = $handler->handle($command);

            return $this->json(['message' => 'Car added successfully.', 'car_id' => $carID], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/car/{registrationNumber}', methods: ['GET'])]
    public function findCar(string $registrationNumber, ValidatorInterface $validator, FindCarHandler $handler): JsonResponse
    {
        try {

            $query = new FindCarQuery($registrationNumber);

            $errors = $validator->validate($query);
            if (count($errors) > 0) {
                return new JsonResponse([
                    'status' => 'error',
                    'errors' => (string) $errors,
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            $car = $handler->handle($query);

            if (!$car) {
                return new JsonResponse(['status' => 'error', 'message' => 'Car not found'], JsonResponse::HTTP_NOT_FOUND);
            }

            return $this->json($car, JsonResponse::HTTP_OK);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'An error occurred while retrieving the car: ' . $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/cars/fit', methods: ['GET'])]
    public function findCarsFitForRoad(FindCarsFitForRoadHandler $handler): JsonResponse
    {
        try {
            $query = new FindCarsFitForRoadQuery();
            
            $cars = $handler->handle($query);

            return $this->json($cars, JsonResponse::HTTP_OK);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'An error occurred while retrieving cars fit for road: ' . $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/cars/unfit', methods: ['GET'])]
    public function findCarsUnfitForRoad(FindCarsUnfitForRoadHandler $handler): JsonResponse
    {
        try {
            $query = new FindCarsUnfitForRoadQuery();
            
            $cars = $handler->handle($query);

            return $this->json($cars, JsonResponse::HTTP_OK);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'An error occurred while retrieving cars unfit for road: ' . $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/car/{registrationNumber}', methods: ['PATCH'])]
    public function updateCar(string $registrationNumber, Request $request, UpdateCarHandler $handler): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $make = $data['make'] ?? null;
        $model = $data['model'] ?? null;

        $command = new UpdateCarCommand($registrationNumber, $make, $model);

        try {
            $handler->handle($command);
            return new JsonResponse(['message' => 'Car updated successfully.'], Response::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (\RuntimeException $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    #[Route('/api/car/{registrationNumber}', methods: ['DELETE'])]
    public function deleteCar(string $registrationNumber, RemoveCarHandler $handler): JsonResponse
    {
        try {
            $command = new RemoveCarCommand($registrationNumber);
            $handler->handle($command);

            return new JsonResponse(['message' => 'Car removed successfully.'], Response::HTTP_OK);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        } catch (\RuntimeException $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
