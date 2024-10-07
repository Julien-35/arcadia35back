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
    
    #[Route('/post', name:'create', methods:['POST'])]
    public function createAvis(Request $request): JsonResponse
    {
        // Récupérer les données JSON envoyées
        $data = json_decode($request->getContent(), true);
    
        // Vérifier si les champs obligatoires sont présents
        if (empty($data) || !isset($data['pseudo'])) {
            return new JsonResponse(['error' => 'pseudo est obligatoire'], Response::HTTP_BAD_REQUEST);
        }
    
        // Créer un nouvel avis
        $avis = new Avis();
        $avis->setPseudo($data['pseudo']);
        $avis->setCommentaire($data['commentaire']) ; 
        $avis->setIsvisible($data['isVisible']); 
    
        // Enregistrer l'avis dans la base de données
        $this->manager->persist($avis);
        $this->manager->flush();
    
        // Retourner une réponse JSON
        return new JsonResponse(['message' => 'Avis a été créé correctement'], Response::HTTP_CREATED);
    }
    
    
    #[Route('/get', name:'show', methods:['GET'])]
    public function show(): JsonResponse
    {
        // Récupérer tous les avis
        $aviss = $this->repository->findAll();
        
        // Vérifier si aucun avis n'est trouvé
        if (empty($aviss)) {
            return new JsonResponse(['message' => 'Aucun avis trouvé'], Response::HTTP_NOT_FOUND);
        }
        
        // Initialiser un tableau pour les avis
        $avissArray = [];
        foreach ($aviss as $avis) {
            $avissArray[] = [
                'id' => $avis->getId(),
                'pseudo' => $avis->getPseudo(),
                'commentaire' => $avis->getCommentaire(),
                'isVisible' => $avis->isIsVisible(), // Assurez-vous que le nom de la méthode est correct
            ];
        }
        
        // Retourner la réponse JSON avec le tableau d'avis
        return new JsonResponse($avissArray, Response::HTTP_OK);
    }

    
    #[Route('/{id}', name:'edit', methods:['PUT'])]
    public function updateAvis(Request $request, $id): JsonResponse
    {
        // Assurez-vous que vous faites référence à la bonne classe
        $avis = $this->repository->find($id);
        
        if (!$avis) {
            return new JsonResponse(['error' => 'Avis non trouvé'], Response::HTTP_NOT_FOUND);
        }
    
        // Récupération des données de la requête
        $data = json_decode($request->getContent(), true);
        
        // Mettre à jour les propriétés si elles sont présentes dans la requête
        if (isset($data['pseudo'])) {
            $avis->setPseudo($data['pseudo']);
        }
        
        if (isset($data['commentaire'])) {
            $avis->setCommentaire($data['commentaire']);
        }
    
        if (isset($data['isVisible'])) {
            $avis->setIsvisible($data['isVisible']);
        } else {
            return new JsonResponse(['error' => 'Missing isVisible parameter'], Response::HTTP_BAD_REQUEST);
        }
    
        // Persistance et mise à jour en base de données
        $this->manager->persist($avis);
        $this->manager->flush();
    
        // Réponse finale
        return new JsonResponse(['message' => 'L\'avis a été mis à jour correctement'], Response::HTTP_OK);
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
