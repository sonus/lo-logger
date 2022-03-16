<?php

namespace App\Controller;

use App\Service\Config\ConfigService;
use App\Service\Log\LogService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteController extends AbstractController
{
    public function __construct(
        private LogService    $logService,
        private ConfigService $configService)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $this->logService->deleteAll();
        $this->configService->deleteAll();

        return $this->json([
            'success' => true
        ], Response::HTTP_OK);
    }
}
