<?php

namespace App\Controller;

use App\Entity\RapportVeterinaire;
use App\Repository\RapportVeterinaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\MediaType;
use OpenApi\Attributes\Schema;


#[Route('/api/rapportveterinaire', name:'app_api_arcadia_rapport_veterinaire_')]
class RapportVeterinaireController extends AbstractController
{
    private EntityManagerInterface $manager;
    private RapportVeterinaireRepository $repository;
    private SerializerInterface $serializer;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        EntityManagerInterface $manager,
        RapportVeterinaireRepository $repository,
        SerializerInterface $serializer,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->serializer = $serializer;
        $this->urlGenerator = $urlGenerator;
    }

    #[Route('', name:'create', methods:['POST'])]
    public function new(Request $request): JsonResponse
    {
        $rapport_veterinaire = $this->serializer->deserialize($request->getContent(), RapportVeterinaire::class, 'json');
        $this->manager->persist($rapport_veterinaire);
        $this->manager->flush();
        
        $responseData = $this->serializer->serialize($rapport_veterinaire, 'json');
        
        $location = $this->urlGenerator->generate(
            'app_api_arcadia_rapport_veterinaire_show',
            ['id' => $rapport_veterinaire->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/get', name:'show', methods:['GET'])]
    public function show(): JsonResponse
    {
        $rapport_veterinaires = $this->repository->findAll();
        $responseData = $this->serializer->serialize($rapport_veterinaires, 'json');

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    #[Route('/{id}', name:'edit', methods:['PUT'])]
    public function edit(int $id, Request $request): JsonResponse
    {
        $rapport_veterinaire = $this->repository->findOneBy(['id' => $id]);
        if ($rapport_veterinaire) {
            $rapport_veterinaire = $this->serializer->deserialize(
                $request->getContent(),
                RapportVeterinaire::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $rapport_veterinaire]
            );
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}', name:'delete', methods:['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $rapport_veterinaire = $this->repository->findOneBy(['id' => $id]);
        if ($rapport_veterinaire) {
            $this->manager->remove($rapport_veterinaire);
            $this->manager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}