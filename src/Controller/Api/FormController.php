<?php

namespace App\Controller\Api;

use App\Service\AutoSchemaBuilder;
use App\Service\FormableRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/form')]
final class FormController extends AbstractController
{
    #[Route('/{path}', methods: ['GET'])]
    public function getSchema(
        string $path, 
        FormableRegistry $registry, 
        AutoSchemaBuilder $schemaBuilder
    ): JsonResponse {
        $className = $registry->getClassByPath($path);
        
        if (!$className) {
            throw $this->createNotFoundException("No Formable entity found for path: $path");
        }

        return $this->json($schemaBuilder->buildSchema($className));
    }

    #[Route('/{path}', methods: ['POST'])]
    public function submitForm(
        string $path, 
        Request $request, 
        FormableRegistry $registry, 
        SerializerInterface $serializer,
        EntityManagerInterface $em
    ): JsonResponse {
        $className = $registry->getClassByPath($path);
        
        if (!$className) {
            throw $this->createNotFoundException("No Formable entity found for path: $path");
        }

        $entity = $serializer->deserialize($request->getContent(), $className, 'json');

        $em->persist($entity);
        $em->flush();

        return $this->json($entity, 201);
    }
}