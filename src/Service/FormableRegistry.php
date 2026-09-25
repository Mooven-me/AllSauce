<?php

namespace App\Service;

use App\Attribute\Formable;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;

class FormableRegistry
{
    public function __construct(private EntityManagerInterface $em) {}

    public function getClassByPath(string $path): ?string
    {
        $metadatas = $this->em->getMetadataFactory()->getAllMetadata();

        foreach ($metadatas as $metadata) {
            $reflection = $metadata->getReflectionClass();
            $attributes = $reflection->getAttributes(Formable::class);

            if (!empty($attributes)) {
                /** @var Formable $formable */
                $formable = $attributes[0]->newInstance();
                
                if ($formable->path === $path) {
                    return $metadata->getName(); // Returns the Fully Qualified Class Name
                }
            }
        }

        return null;
    }
}