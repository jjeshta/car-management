<?php

namespace App\Presentation\Controller;

use App\Application\Command\AddServiceHistoryCommand;
use App\Application\Command\RemoveServiceHistoryCommand;
use App\Application\CommandHandler\RemoveServiceHistoryHandler;
use App\Application\CommandHandler\AddServiceHistoryHandler;
use App\Application\DTO\ServiceHistoryDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ServiceHistoryController extends AbstractController
{
    #[Route('/api/service-history', methods: ['POST'])]
    public function add(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, AddServiceHistoryHandler $handler): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $serviceHistoryDTO = $serializer->deserialize($request->getContent(), ServiceHistoryDTO::class, 'json');

            $errors = $validator->validate($serviceHistoryDTO);

            if (count($errors) > 0) {
                return new JsonResponse([
                    'status' => 'error',
                    'errors' => (string) $errors,
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            $command = new AddServiceHistoryCommand($serviceHistoryDTO);
            $handler->handle($command);

            return new JsonResponse(['message' => 'Service history added successfully.'], JsonResponse::HTTP_CREATED);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/service-history/{id}', methods: ['DELETE'])]
    public function delete(int $id, RemoveServiceHistoryHandler $handler): JsonResponse
    {
        try {
            $command = new RemoveServiceHistoryCommand($id);
            $handler->handle($command);

            return new JsonResponse(['status' => 'success', 'message' => 'Service history deleted successfully.'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], JsonResponse::HTTP_NOT_FOUND);
        }
    }
}
