<?php

namespace App\Controller;

use App\Service\RedisService;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/image', name: 'app_api_image_')]
class ImageController extends AbstractController
{
    private RedisService $redisService;

    public function __construct(RedisService $redisService)
    {
        $this->redisService = $redisService;
    }

    #[Route('/click', name: 'click', methods: ['POST'])]
    public function click(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $imageName = $data['imageName'] ?? null;

        if ($imageName) {
            $this->redisService->incrementImageClicks($imageName);
            $clicks = $this->redisService->getImageClicks($imageName);
            return new JsonResponse(['clicks' => $clicks]);
        }

        return new JsonResponse(['error' => 'Image name is required'], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/clicks', name: 'get_clicks', methods: ['GET'])]
    public function getClicks(): JsonResponse
    {
        $imageNames = ['image1', 'image2', 'image3']; // Liste des noms d'image connus
        $clicksData = [];

        foreach ($imageNames as $imageName) {
            $clicksData[$imageName] = $this->redisService->getImageClicks($imageName);
        }

        return new JsonResponse($clicksData);
    }
}
