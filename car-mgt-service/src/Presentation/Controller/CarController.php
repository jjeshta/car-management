<?php

namespace App\Presentation\Controller;

use App\Application\Command\AddCarCommand;
use App\Application\CommandHandler\AddCarHandler;
use App\Application\DTO\CarDTO;
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
    
}
