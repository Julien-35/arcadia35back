<?php

namespace App\Controller;

use App\Entity\Habitat;
use DateTime;
use DateTimeImmutable;
use App\Repository\HabitatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/habitat', name:'app_api_arcadia_habitat_')]
class HabitatController extends AbstractController
{
    private EntityManagerInterface $manager;
    private HabitatRepository $repository;

    public function __construct(
        EntityManagerInterface $manager,
        HabitatRepository $repository
    ) {
        $this->manager = $manager;
        $this->repository = $repository;
    }

    #[Route('/post', name:'create', methods:['POST'])]
    public function createHabitat(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validation des données
        if (empty($data['nom'])) {
            return new JsonResponse(['error' => 'nom is required'], Response::HTTP_BAD_REQUEST);
        }

        $habitat = new Habitat();
        $habitat->setNom($data['nom']);
        $habitat->setDescription($data['description'] ?? null);
        $habitat->setCommentaireHabitat($data['commentaire_habitat'] ?? null);

        $habitat->setImageData($data['image_data'] ?? null);

        $this->manager->persist($habitat);
        $this->manager->flush();

        return new JsonResponse(['message' => 'Habitat created successfully'], Response::HTTP_CREATED);
    }


    #[Route('/get', name: 'show', methods: ['GET'])]
    public function show(): JsonResponse
    {
        $habitats = $this->repository->findAll();
    
        if (empty($habitats)) {
            return new JsonResponse(['message' => 'Aucun habitat trouvé'], Response::HTTP_NOT_FOUND);
        }
    
        $habitatsArray = [];
        foreach ($habitats as $habitat) {
            $habitatsArray[] = [
                'id' => $habitat->getId(),
                'nom' => $habitat->getNom(),
                'description' => $habitat->getDescription(),
                'commentaire_habitat' => $habitat->getCommentaireHabitat(),
                'image_data' => $habitat->getImageData()
            ];
        }
        // Log pour déboguer la réponse
        $response = new JsonResponse($habitatsArray, Response::HTTP_OK);
        return $response; 
    }

    #[Route('/{id}', name:'edit', methods:['PUT'])]
    public function updateHabitat(Request $request, $id): JsonResponse
    {
        $habitat = $this->manager->getRepository(Habitat::class)->find($id);

        if (!$habitat) {
            return new JsonResponse(['error' => 'Habitat not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['nom'])) {
            $habitat->setNom($data['nom']);
        }

        if (isset($data['description'])) {
            $habitat->setDescription($data['description']);
        }

        if (isset($data['image_data'])) {
            $habitat->setImageData($data['image_data']);
        }
        
        $this->manager->persist($habitat);
        $this->manager->flush();

        return new JsonResponse(['message' => 'la habitat a été mis à jour correctement'], Response::HTTP_OK);
    }


    #[Route('/{id}', name:'delete', methods:['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $habitat = $this->repository->find($id);
        if ($habitat) {
            $this->manager->remove($habitat);
            $this->manager->flush();
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(['error' => 'Habitat not found'], Response::HTTP_NOT_FOUND);
    }
}
