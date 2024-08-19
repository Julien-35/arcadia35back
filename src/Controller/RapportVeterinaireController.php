<?php

namespace App\Controller;

use App\Entity\RapportVeterinaire;
use App\Repository\RapportVeterinaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/rapportveterinaire', name: 'app_api_rapport_veterinaire_')]
class RapportVeterinaireController extends AbstractController
{
    private EntityManagerInterface $manager;
    private RapportVeterinaireRepository $repository;

    public function __construct(
        EntityManagerInterface $manager,
        RapportVeterinaireRepository $repository
    ) {
        $this->manager = $manager;
        $this->repository = $repository;
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['detail']) || empty($data['animal_id'])) {
            return new JsonResponse(['error' => 'Detail and animal_id are required'], Response::HTTP_BAD_REQUEST);
        }

        $animal = $this->manager->getRepository(Animal::class)->find($data['animal_id']);

        if (!$animal) {
            return new JsonResponse(['error' => 'Animal not found'], Response::HTTP_NOT_FOUND);
        }

        $rapport = new RapportVeterinaire();
        $rapport->setDetail($data['detail']);
        $rapport->setDate(new \DateTime()); 

        $rapport->setAnimal($animal);

        $this->manager->persist($rapport);
        $this->manager->flush();

        return new JsonResponse(['message' => 'Rapport vétérinaire créé avec succès'], Response::HTTP_CREATED);
    }

    #[Route('/get', name: 'show', methods: ['GET'])]
    public function show(): JsonResponse
    {
        $rapports = $this->repository->findAll(); // Récupère tous les rapports vétérinaires
    
        if (empty($rapports)) {
            return new JsonResponse(['error' => 'No rapport vétérinaire found'], Response::HTTP_NOT_FOUND);
        }
    
        $rapportsData = array_map(function ($rapport) {
            return [
                'id' => $rapport->getId(), // Ajoutez l'ID si nécessaire
                'date' => $rapport->getDate()->format('d-m-Y'),
                'detail' => $rapport->getDetail(),
                'animal_prenom' => $rapport->getAnimal() ? $rapport->getAnimal()->getPrenom() : null
            ];
        }, $rapports);
    
        return new JsonResponse($rapportsData, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $rapport = $this->repository->find($id);

        if (!$rapport) {
            return new JsonResponse(['error' => 'Rapport vétérinaire not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['detail'])) {
            $rapport->setDetail($data['detail']);
        }
        if (isset($data['date'])) {
            try {
                $date = new \DateTime($data['date']);
                $rapport->setDate($date);
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Invalid date format'], Response::HTTP_BAD_REQUEST);
            }
        }
        if (isset($data['animal_id'])) {
            $animal = $this->manager->getRepository(Animal::class)->find($data['animal_id']);
            if (!$animal) {
                return new JsonResponse(['error' => 'Animal not found'], Response::HTTP_NOT_FOUND);
            }
            $rapport->setAnimal($animal);
        }

        $this->manager->persist($rapport);
        $this->manager->flush();

        return new JsonResponse(['message' => 'Rapport vétérinaire mis à jour avec succès'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $rapport = $this->repository->find($id);

        if (!$rapport) {
            return new JsonResponse(['error' => 'Rapport vétérinaire not found'], Response::HTTP_NOT_FOUND);
        }

        $this->manager->remove($rapport);
        $this->manager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
