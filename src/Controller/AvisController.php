<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/avis', name:'app_api_arcadia_avis_')]
class AvisController extends AbstractController
{
    private EntityManagerInterface $manager;
    private AvisRepository $repository;

    public function __construct(
        EntityManagerInterface $manager,
        AvisRepository $repository
    ) {
        $this->manager = $manager;
        $this->repository = $repository;
    }

    #[Route('', name:'create', methods:['POST'])]
    public function createAvis(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validation des données
        if (empty($data['pseudo'])) {
            return new JsonResponse(['error' => 'pseudo is required'], Response::HTTP_BAD_REQUEST);
        }

        $avis = new Avis();
        $avis->setPseudo($data['pseudo']);
        $avis->setCommentaire($data['commentaire'] ?? null);
        $avis->setIsvisible($data['isvisible'] ?? null);


        $this->manager->persist($avis);
        $this->manager->flush();

        return new JsonResponse(['message' => 'Avis created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/get', name:'show', methods:['GET'])]
    public function show(): JsonResponse
    {
        $aviss = $this->repository->findAll();
    
        if (empty($aviss)) {
            return new JsonResponse(['message' => 'Aucun avis trouvé'], Response::HTTP_NOT_FOUND);
        }
    
        $avissArray = [];
        foreach ($aviss as $avis) {
            $avissArray[] = [
                'id' => $avis->getId(),
                'pseudo' => $avis->getPseudo(),
                'commentaire' => $avis->getCommentaire(),
                'isVisible' => $avis->isIsvisible(),
            ];
        }
    
        return new JsonResponse($avissArray, Response::HTTP_OK);
    }

    #[Route('/{id}', name:'edit', methods:['PUT'])]
    public function updateAvis(Request $request, $id): JsonResponse
    {
        $avis = $this->manager->getRepository(Avis::class)->find($id);

        if (!$avis) {
            return new JsonResponse(['error' => 'Avis not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['pseudo'])) {
            $avis->setPseudo($data['pseudo']);
        }
        if (isset($data['commentaire'])) {
            $avis->setCommentaire($data['commentaire']);
        }
        if (isset($data['isvisible'])) {
            $avis->setIsvisible($data['isvisible']);
        }
        
        $this->manager->persist($avis);
        $this->manager->flush();

        return new JsonResponse(['message' => 'Avis mis à jour correctement'], Response::HTTP_OK);
    }

    #[Route('/{id}', name:'delete', methods:['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $avis = $this->repository->find($id);
        if ($avis) {
            $this->manager->remove($avis);
            $this->manager->flush();
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(['error' => 'Avis not found'], Response::HTTP_NOT_FOUND);
    }
}
