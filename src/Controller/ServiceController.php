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

    #[Route('/post', name:'create', methods:['POST'])]
    public function createService(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        // Vérification de la présence du champ 'nom'
        if (empty($data['nom'])) {
            return new JsonResponse(['error' => 'nom est obligatoire'], Response::HTTP_BAD_REQUEST);
        }
    
        // Création d'une nouvelle instance de Service
        $service = new Service();
    
        // Nettoyer et valider les données d'entrée
        $service->setNom($this->sanitizeInput($data['nom']));
        $service->setDescription($this->sanitizeInput($data['description'] ?? ''));
        $service->setImageData($data['image_data'] ?? null);
    
        // Persist the service
        $this->manager->persist($service);
        $this->manager->flush();
    
        return new JsonResponse(['message' => 'Le service a été créé correctement'], Response::HTTP_CREATED);
    }

 #[Route('/get', name: 'show', methods: ['GET'])]
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
    // Log pour déboguer la réponse
    $response = new JsonResponse($servicesArray, Response::HTTP_OK);
    return $response; 
}

#[Route('/{id}', name: 'edit', methods: ['PUT'])]
public function updateService(Request $request, int $id): JsonResponse
{
    // Récupérer le service par ID
    $service = $this->repository->find($id);
    
    if (!$service) {
        return new JsonResponse(['error' => 'Service non trouvé'], Response::HTTP_NOT_FOUND);
    }

    // Récupérer et décoder les données JSON
    $data = json_decode($request->getContent(), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return new JsonResponse(['error' => 'Données JSON invalides'], Response::HTTP_BAD_REQUEST);
    }

    // Mise à jour des propriétés du service
    if (isset($data['nom'])) {
        $service->setNom($this->sanitizeInput($data['nom']));
    }

    if (isset($data['description'])) {
        $service->setDescription($this->sanitizeInput($data['description']));
    }

    if (isset($data['image_data'])) {
        $service->setImageData($data['image_data']);
    }

    // Vérifiez qu'au moins un champ a été mis à jour
    if (!isset($data['nom']) && !isset($data['description']) && !isset($data['image_data'])) {
        return new JsonResponse(['error' => 'Au moins un champ à mettre à jour est requis.'], Response::HTTP_BAD_REQUEST);
    }

    // Persistance et mise à jour en base de données
    $this->manager->persist($service);
    $this->manager->flush();

    // Renvoyer la réponse JSON sans caractères indésirables
    return new JsonResponse(['message' => 'Le service a été mis à jour correctement'], Response::HTTP_OK);
}

    
    

#[Route('/{id}', name: 'delete', methods: ['DELETE'])]
public function delete(Request $request, int $id): JsonResponse
{
    // Vérification de l'autorisation
    if (!$this->isGranted('ROLE_ADMIN')) {
        return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
    }

    // Récupérer le service par ID
    $service = $this->repository->find($id);
    if ($service) {
        // Supprimer le service
        $this->manager->remove($service);
        $this->manager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
    return new JsonResponse(['error' => 'Service not found'], Response::HTTP_NOT_FOUND);
}
        // Fonction pour nettoyer les entrées utilisateur
    private function sanitizeInput(string $input): string
    {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

}

