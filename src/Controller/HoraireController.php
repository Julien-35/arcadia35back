<?php

namespace App\Controller;

use App\Entity\Horaire;
use DateTime;
use DateTimeImmutable;
use App\Repository\HoraireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/horaire', name:'app_api_arcadia_horaire_')]
class HoraireController extends AbstractController
{
    private EntityManagerInterface $manager;
    private HoraireRepository $repository;

    public function __construct(
        EntityManagerInterface $manager,
        HoraireRepository $repository
    ) {
        $this->manager = $manager;
        $this->repository = $repository;
    }

    #[Route('', name:'create', methods:['POST'])]
    public function createHoraire(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validation des données
        if (empty($data['titre'])) {
            return new JsonResponse(['error' => 'titre is required'], Response::HTTP_BAD_REQUEST);
        }

        $horaire = new Horaire();
        $horaire->setTitre($data['titre']);
        $horaire->setMessage($data['message'] ?? null);
        $horaire->setHeureDebut($data['heure_debut'] ?? null);
        $horaire->setHeureFin($data['heure_fin'] ?? null);
        $horaire->setJour($data['jour'] ?? null);


        $this->manager->persist($horaire);
        $this->manager->flush();

        return new JsonResponse(['message' => 'Horaire created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/get', name:'show', methods:['GET'])]
    public function show(): JsonResponse
    {
        // Récupérer tous les horaire
        $horaires = $this->repository->findAll();
        
        // Vérifier si aucun horaire n'est trouvé
        if (empty($horaires)) {
            return new JsonResponse(['message' => 'Aucun horaire trouvé'], Response::HTTP_NOT_FOUND);
        }
        
        // Initialiser un tableau pour les horaire
        $horairesArray = [];
        foreach ($horaires as $horaire) {
            $horairesArray[] = [
                'id' => $horaire->getId(),
                'titre' => $horaire->getTitre(),
                'message' => $horaire->getMessage(),
                'heure_debut' => $horaire->getHeureDebut(),
                'heure_fin' => $horaire->getHeureFin(),
                'jour' => $horaire->getJour(), // Assurez-vous que le nom de la méthode est correct
            ];
        }
        
        // Retourner la réponse JSON avec le tableau d'horaire
        return new JsonResponse($horairesArray, Response::HTTP_OK);
    }

    #[Route('/{id}', name:'edit', methods:['PUT'])]
    public function updateHoraire(Request $request, $id): JsonResponse
    {
        $horaire = $this->manager->getRepository(Horaire::class)->find($id);
    
        if (!$horaire) {
            return new JsonResponse(['error' => 'Horaire not found'], Response::HTTP_NOT_FOUND);
        }
    
        $data = json_decode($request->getContent(), true);
    
        // Sanitize and validate input
        if (isset($data['titre'])) {
            $horaire->setTitre($this->sanitizeInput($data['titre']));
        }
        if (isset($data['message'])) {
            $horaire->setMessage($this->sanitizeInput($data['message']));
        }
        if (isset($data['heure_debut'])) {
            // Assurez-vous que l'heure_debut est dans un format valide, par exemple, une date/heure
            $horaire->setHeureDebut($data['heure_debut']);
        }
        if (isset($data['heure_fin'])) {
            // Assurez-vous que l'heure_fin est dans un format valide, par exemple, une date/heure
            $horaire->setHeureFin($data['heure_fin']);
        }
        if (isset($data['jour'])) {
            $horaire->setJour($this->sanitizeInput($data['jour']));
        }
    
        $this->manager->persist($horaire);
        $this->manager->flush();
    
        return new JsonResponse(['message' => 'Horaire mis à jour correctement'], Response::HTTP_OK);
    }
    
    // Fonction pour nettoyer les entrées utilisateur
    private function sanitizeInput(string $input): string
    {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    
    #[Route('/{id}', name:'delete', methods:['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $horaire = $this->repository->find($id);
        if ($horaire) {
            $this->manager->remove($horaire);
            $this->manager->flush();
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(['error' => 'Horaire not found'], Response::HTTP_NOT_FOUND);
    }
}
