<?php

namespace App\Controller;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Service\Log\LogService;
use App\Service\Log\SearchLogDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CountController extends AbstractController
{

    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface  $validator,
        private LogService          $logService,
    )
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $searchLogDto = $this->serializer->deserialize(
                json_encode($request->query->all()),
                SearchLogDto::class,
                "json"
            );
        } catch (\Exception $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }

        $errors = $this->validator->validate($searchLogDto);
        if (count($errors)) {
            throw new ValidationException($errors);
        }

        return $this->json([
            'count' => $this->logService
                ->getCount($searchLogDto)
        ], Response::HTTP_OK);
    }
}
