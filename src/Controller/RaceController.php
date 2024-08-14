<?php

namespace App\Controller;

use App\Entity\Race;
use App\Repository\RaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/race', name:'app_api_arcadia_race_')]
class RaceController extends AbstractController
{
    private EntityManagerInterface $manager;
    private RaceRepository $repository;

    public function __construct(
        EntityManagerInterface $manager,
        RaceRepository $repository
    ) {
        $this->manager = $manager;
        $this->repository = $repository;
    }

    #[Route('', name:'create', methods:['POST'])]
    public function createRace(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validation des données
        if (empty($data['label'])) {
            return new JsonResponse(['error' => 'label is required'], Response::HTTP_BAD_REQUEST);
        }

        $race = new Race();
        $race->setLabel($data['label']);



        $this->manager->persist($race);
        $this->manager->flush();

        return new JsonResponse(['message' => 'La Race a été créé correctement'], Response::HTTP_CREATED);
    }

    #[Route('/get', name:'show', methods:['GET'])]
    public function show(): JsonResponse
    {
        $races = $this->repository->findAll();
    
        if (empty($races)) {
            return new JsonResponse(['message' => 'Aucun race trouvé'], Response::HTTP_NOT_FOUND);
        }
    
        $racesArray = [];
        foreach ($races as $race) {
            $racesArray[] = [
                'id' => $race->getId(),
                'label' => $race->getLabel(),
            ];
        }
    
        return new JsonResponse($racesArray, Response::HTTP_OK);
    }

    #[Route('/{id}', name:'edit', methods:['PUT'])]
    public function updateRace(Request $request, $id): JsonResponse
    {
        $race = $this->manager->getRepository(Race::class)->find($id);

        if (!$race) {
            return new JsonResponse(['error' => 'Race not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['label'])) {
            $race->setLabel($data['label']);
        }
        $this->manager->persist($race);
        $this->manager->flush();

        return new JsonResponse(['message' => 'la race a été mis à jour correctement'], Response::HTTP_OK);
    }

    #[Route('/{id}', name:'delete', methods:['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $race = $this->repository->find($id);
        if ($race) {
            $this->manager->remove($race);
            $this->manager->flush();
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(['error' => 'Race not found'], Response::HTTP_NOT_FOUND);
    }
}
