<?php

namespace App\Controller;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/role', name:'app_api_arcadia_role_')]
class RoleController extends AbstractController
{
    private EntityManagerInterface $manager;
    private RoleRepository $repository;

    public function __construct(
        EntityManagerInterface $manager,
        RoleRepository $repository
    ) {
        $this->manager = $manager;
        $this->repository = $repository;
    }

    #[Route('/post', name:'create', methods:['POST'])]
    public function createRole(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validation des données
        if (empty($data['label'])) {
            return new JsonResponse(['error' => 'label is required'], Response::HTTP_BAD_REQUEST);
        }

        $role = new Role();
        $role->setLabel($data['label']);



        $this->manager->persist($role);
        $this->manager->flush();

        return new JsonResponse(['message' => 'La Role a été créé correctement'], Response::HTTP_CREATED);
    }

    #[Route('/get', name:'show', methods:['GET'])]
    public function show(): JsonResponse
    {
        $roles = $this->repository->findAll();
    
        if (empty($roles)) {
            return new JsonResponse(['message' => 'Aucun role trouvé'], Response::HTTP_NOT_FOUND);
        }
    
        $rolesArray = [];
        foreach ($roles as $role) {
            $rolesArray[] = [
                'id' => $role->getId(),
                'label' => $role->getLabel(),
            ];
        }
    
        return new JsonResponse($rolesArray, Response::HTTP_OK);
    }

    #[Route('/{id}', name:'edit', methods:['PUT'])]
    public function updateRole(Request $request, $id): JsonResponse
    {
        $role = $this->manager->getRepository(Role::class)->find($id);

        if (!$role) {
            return new JsonResponse(['error' => 'Role not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['label'])) {
            $role->setLabel($data['label']);
        }
        $this->manager->persist($role);
        $this->manager->flush();

        return new JsonResponse(['message' => 'la role a été mis à jour correctement'], Response::HTTP_OK);
    }

    #[Route('/{id}', name:'delete', methods:['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $role = $this->repository->find($id);
        if ($role) {
            $this->manager->remove($role);
            $this->manager->flush();
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(['error' => 'Role not found'], Response::HTTP_NOT_FOUND);
    }
}
