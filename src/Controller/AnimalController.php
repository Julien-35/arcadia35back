<?php

namespace App\Controller;

use App\Entity\Animal;
use DateTime;
use DateTimeImmutable;
use App\Repository\AnimalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/animal', name:'app_api_arcadia_animal_')]
class AnimalController extends AbstractController
{
    private EntityManagerInterface $manager;
    private AnimalRepository $repository;

    public function __construct(
        EntityManagerInterface $manager,
        AnimalRepository $repository
    ) {
        $this->manager = $manager;
        $this->repository = $repository;
    }

    #[Route('', name:'create', methods:['POST'])]
    public function createAnimal(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validation des données
        if (empty($data['Prenom'])) {
            return new JsonResponse(['error' => 'Prenom is required'], Response::HTTP_BAD_REQUEST);
        }

        $animal = new Animal();
        $animal->setPrenom($data['Prenom']);
        $animal->setEtat($data['etat'] ?? null);
        $animal->setNourriture($data['nourriture'] ?? null);

        $animal->setGrammage($data['grammage'] ?? null);

        // Convertir feeding_time en DateTime
        if (isset($data['feeding_time'])) {
            try {
                $feedingTime = new DateTime($data['feeding_time']);
                $animal->setFeedingTime($feedingTime);
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Invalid feeding_time format'], Response::HTTP_BAD_REQUEST);
            }
        }

        // Convertir created_at en DateTimeImmutable
        if (isset($data['created_at'])) {
            try {
                $createdAt = new DateTimeImmutable($data['created_at']);
                $animal->setCreatedAt($createdAt);
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Invalid created_at format'], Response::HTTP_BAD_REQUEST);
            }
        }

        $animal->setImageData($data['image_data'] ?? null);

        $this->manager->persist($animal);
        $this->manager->flush();

        return new JsonResponse(['message' => 'L'/'animal a été créé correctement'], Response::HTTP_CREATED);
    }

    #[Route('/get', name: 'show', methods: ['GET'])]
    public function show(Request $request): JsonResponse
    {
        // Récupérer le paramètre habitat_id depuis la requête
        $habitatId = $request->query->get('habitat_id');
    
        // Vérifier si habitat_id est présent et récupérer les animaux associés
        if ($habitatId) {
            $animals = $this->repository->findBy(['habitat' => $habitatId]);
        } else {
            $animals = $this->repository->findAll();
        }
    
        $animalsArray = [];
        
        foreach ($animals as $animal) {
            $animalData = [
                'id' => $animal->getId(),
                'prenom' => $animal->getPrenom(),
                'etat' => $animal->getEtat(),
                'nourriture' => $animal->getNourriture(),
                'grammage' => $animal->getGrammage(),
                'feeding_time' => $animal->getFeedingTime() ? $animal->getFeedingTime()->format('H:i') : null,
                'created_at' => $animal->getCreatedAt() ? $animal->getCreatedAt()->format('Y-m-d\TH:i:s') : null,
                'image_data' => $animal->getImageData(),
                'habitat' => $animal->getHabitat() ? $animal->getHabitat()->getNom() : null,
                'race' => $animal->getRace() ? $animal->getRace()->getLabel() : null,
                // Inclure uniquement si la collection des rapports vétérinaires n'est pas vide
                'rapport_veterinaire' => $animal->getRapportVeterinaire()->isEmpty() ? [] : array_map(function ($rapport) {
                    return [
                        'detail' => $rapport->getDetail()
                    ];
                }, $animal->getRapportVeterinaire()->toArray())
            ];
    
            $animalsArray[] = $animalData;
        }
    
        return new JsonResponse($animalsArray, Response::HTTP_OK);
    }
    
    

    #[Route('/{id}', name:'edit', methods:['PUT'])]
    public function updateAnimal(Request $request, $id): JsonResponse
    {
        $animal = $this->manager->getRepository(Animal::class)->find($id);

        if (!$animal) {
            return new JsonResponse(['error' => 'Animal not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['prenom'])) {
            $animal->setPrenom($data['prenom']);
        }
        if (isset($data['etat'])) {
            $animal->setEtat($data['etat']);
        }
        if (isset($data['nourriture'])) {
            $animal->setNourriture($data['nourriture']);
        }
        if (isset($data['grammage'])) {
            $animal->setGrammage($data['grammage']);
        }
        if (isset($data['feeding_time'])) {
            try {
                $feedingTime = new DateTime($data['feeding_time']);
                $animal->setFeedingTime($feedingTime);
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Invalid feeding_time format'], Response::HTTP_BAD_REQUEST);
            }
        }
        if (isset($data['created_at'])) {
            try {
                $createdAt = new DateTimeImmutable($data['created_at']);
                $animal->setCreatedAt($createdAt);
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Invalid created_at format'], Response::HTTP_BAD_REQUEST);
            }
        }
        if (isset($data['image_data'])) {
            $animal->setImageData($data['image_data']);
        }

        $this->manager->persist($animal);
        $this->manager->flush();

        return new JsonResponse(['message' => 'Animal mis à jour correctement'], Response::HTTP_OK);
    }

    #[Route('/{id}', name:'delete', methods:['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $animal = $this->repository->find($id);
        if ($animal) {
            $this->manager->remove($animal);
            $this->manager->flush();
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(['error' => 'Animal not found'], Response::HTTP_NOT_FOUND);
    }
}
