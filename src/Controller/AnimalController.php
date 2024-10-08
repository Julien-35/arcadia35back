<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Habitat;
use App\Repository\HabitatRepository;
use App\Entity\Race;
use App\Repository\RaceRepository;
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
    private HabitatRepository $habitatRepository;
    private RaceRepository $raceRepository;


    public function __construct(
        EntityManagerInterface $manager,
        AnimalRepository $repository,
        HabitatRepository $habitatRepository,
        RaceRepository $raceRepository

    ) {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->habitatRepository = $habitatRepository;
        $this->raceRepository = $raceRepository;
    }

    #[Route('/post', name:'create', methods:['POST'])]
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

        // Validation et récupération de l'habitat
        if (isset($data['habitat_id'])) {
            $habitat = $this->habitatRepository->find($data['habitat_id']);
            if (!$habitat) {
                return new JsonResponse(['error' => 'Invalid habitat_id'], Response::HTTP_BAD_REQUEST);
            }
            $animal->setHabitat($habitat);
        } else {
            return new JsonResponse(['error' => 'habitat_id is required'], Response::HTTP_BAD_REQUEST);
        }
        
        // Validation et récupération de la race
        if (isset($data['race_id'])) {
            $race = $this->raceRepository->find($data['race_id']);
            if (!$race) {
                return new JsonResponse(['error' => 'Invalid race_id'], Response::HTTP_BAD_REQUEST);
            }
            $animal->setRace($race);
        } else {
            return new JsonResponse(['error' => 'race_id is required'], Response::HTTP_BAD_REQUEST);
        }

        $animal->setImageData($data['image_data'] ?? null);

        try {
            $this->manager->persist($animal);
            $this->manager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while saving the animal: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Animal created successfully'], Response::HTTP_CREATED);
    }

    
    #[Route('/get', name: 'show', methods: ['GET'])]
    public function show(Request $request): JsonResponse
    {
        // Récupérer les paramètres de requête
        $habitatId = $request->query->get('habitat_id');
        $raceId = $request->query->get('race_id');
        
        $criteria = [];
        
        // Ajouter des critères de recherche basés sur les paramètres de la requête
        if ($habitatId) {
            $criteria['habitat'] = $habitatId;
        }
        
        if ($raceId) {
            $criteria['race'] = $raceId;
        }
    
        // Trouver les animaux en fonction des critères
        $animals = $this->repository->findBy($criteria);
    
        // Vérifier si des animaux ont été trouvés
        if (empty($animals)) {
            return new JsonResponse(['message' => 'Aucun animal trouvé'], Response::HTTP_NOT_FOUND);
        }
    
        // Préparer le tableau de réponse
        $animalsArray = [];
    
        foreach ($animals as $animal) {
            // Créer les données de chaque animal
            $animalData = [
                'id' => $animal->getId(),
                'prenom' => $animal->getPrenom(),
                'etat' => $animal->getEtat(),
                'nourriture' => $animal->getNourriture(),
                'grammage' => $animal->getGrammage(),
                'feeding_time' => $animal->getFeedingTime() ? $animal->getFeedingTime()->format('H:i') : null,
                'created_at' => $animal->getCreatedAt() ? $animal->getCreatedAt()->format('d-m-Y') : null,
                'image_data' => $animal->getImageData(),
                'habitat' => $animal->getHabitat() ? $animal->getHabitat()->getNom() : null,  // Vérification de l'existence du habitat
                'race' => $animal->getRace() ? $animal->getRace()->getLabel() : null,        // Vérification de l'existence de la race
                'rapport_veterinaire' => $animal->getRapportVeterinaire()->isEmpty() ? [] : array_map(function ($rapport) {
                    return [
                        'detail' => $rapport->getDetail()
                    ];
                }, $animal->getRapportVeterinaire()->toArray())
            ];
    
            // Ajouter les données de l'animal au tableau
            $animalsArray[] = $animalData;
        }
    
        // Retourner la réponse JSON avec le tableau des animaux
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
    
        // Renvoyer les nouvelles données de l'animal mis à jour
        return new JsonResponse([
            'message' => 'Animal mis à jour correctement',
            'animal' => [
                'id' => $animal->getId(),
                'prenom' => $animal->getPrenom(),
                'etat' => $animal->getEtat(),
                'nourriture' => $animal->getNourriture(),
                'grammage' => $animal->getGrammage(),
                'feeding_time' => $animal->getFeedingTime()->format('H:i'), // format "HH:mm"
                'created_at' => $animal->getCreatedAt()->format('Y-m-d'), // format "YYYY-MM-DD"
                'image_data' => $animal->getImageData(),
            ]
        ], Response::HTTP_OK);
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
