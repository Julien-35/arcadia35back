<?php

namespace App\Controller;

use App\Entity\Service;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/service', name:'app_api_arcadia_service_')]
class ServiceController extends AbstractController
{
    private EntityManagerInterface $manager;
    private ServiceRepository $repository;

    public function __construct(
        EntityManagerInterface $manager,
        ServiceRepository $repository
    ) {
        $this->manager = $manager;
        $this->repository = $repository;
    }

    #[Route('', name:'create', methods:['POST'])]
    public function createService(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validation des données
        if (empty($data['nom'])) {
            return new JsonResponse(['error' => 'nom est obligatoire'], Response::HTTP_BAD_REQUEST);
        }

        $service = new Service();
        $service->setNom($data['nom']);
        $service->setDescription($data['description']);
        $service->setImageData($data['image_data'] ?? null);

        $this->manager->persist($service);
        $this->manager->flush();

        return new JsonResponse(['message' => 'La Service a été créé correctement'], Response::HTTP_CREATED);
    }

    #[Route('/get', name:'show', methods:['GET'])]
    public function show(): JsonResponse
    {
        $services = $this->repository->findAll();
    
        if (empty($services)) {
            return new JsonResponse(['message' => 'Aucun service trouvé'], Response::HTTP_NOT_FOUND);
        }
    
        $servicesArray = [];
        foreach ($services as $service) {
            $servicesArray[] = [
                'id' => $service->getId(),
                'nom' => $service->getNom(),
                'description' => $service->getDescription(),
                'image_data' => $service->getImageData(),
            ];
        }
    
        return new JsonResponse($servicesArray, Response::HTTP_OK);
    }

    #[Route('/{id}', name:'edit', methods:['PUT'])]
    public function updateService(Request $request, $id): JsonResponse
    {
        $service = $this->manager->getRepository(Service::class)->find($id);

        if (!$service) {
            return new JsonResponse(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['nom'])) {
            $service->setNom($data['nom']);
        }

        if (isset($data['description'])) {
            $service->setDescription($data['description']);
        }

        if (isset($data['image_data'])) {
            $service->setImageData($data['image_data']);
        }
        
        $this->manager->persist($service);
        $this->manager->flush();

        return new JsonResponse(['message' => 'la service a été mis à jour correctement'], Response::HTTP_OK);
    }

    #[Route('/{id}', name:'delete', methods:['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $service = $this->repository->find($id);
        if ($service) {
            $this->manager->remove($service);
            $this->manager->flush();
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
    }
}
