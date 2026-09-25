<?php

namespace App\Service;

use App\Attribute\NotFormable;
use ReflectionClass;
use ReflectionNamedType;

class AutoSchemaBuilder
{
    /**
     * Builds a JSON Schema array dynamically from an entity class.
     */
    public function buildSchema(string $className, array $processedClasses = []): array
    {
        if (in_array($className, $processedClasses, true)) {
            return [];
        }
        $processedClasses[] = $className;

        $reflection = new ReflectionClass($className);
        
        $schema = [
            'type' => 'object',
            'title' => $reflection->getShortName(),
            'properties' => [],
            'required' => [],
        ];

        foreach ($reflection->getProperties() as $property) {
            if (count($property->getAttributes(NotFormable::class)) > 0) {
                continue;
            }

            $name = $property->getName();
            $type = $property->getType();
            $allowsNull = $type ? $type->allowsNull() : true;

            if (!$allowsNull) {
                $schema['required'][] = $name;
            }

            if (!$type instanceof ReflectionNamedType) {
                $schema['properties'][$name] = ['type' => 'string', 'title' => ucfirst($name)];
                continue;
            }

            $typeName = $type->getName();

            switch ($typeName) {
                case 'string':
                    $schema['properties'][$name] = ['type' => 'string', 'title' => ucfirst($name)];
                    break;
                case 'int':
                    $schema['properties'][$name] = ['type' => 'integer', 'title' => ucfirst($name)];
                    break;
                case 'float':
                    $schema['properties'][$name] = ['type' => 'number', 'title' => ucfirst($name)];
                    break;
                case 'bool':
                    $schema['properties'][$name] = ['type' => 'boolean', 'title' => ucfirst($name)];
                    break;
                case '\DateTime':
                case '\DateTimeImmutable':
                case 'DateTime':
                case 'DateTimeImmutable':
                    $schema['properties'][$name] = [
                        'type' => 'string', 
                        'format' => 'date-time', 
                        'title' => ucfirst($name)
                    ];
                    break;
                default:
                    if (enum_exists($typeName)) {
                        $cases = $typeName::cases();
                        $isBacked = is_subclass_of($typeName, \BackedEnum::class);
                        
                        $oneOf = [];
                        foreach ($cases as $case) {
                            $val = $isBacked ? $case->value : $case->name;
                            $oneOf[] = [
                                'const' => $val,
                                'title' => $case->name
                            ];
                        }

                        $schema['properties'][$name] = [
                            'type' => (!empty($cases) && $isBacked && is_int($cases[0]->value)) ? 'integer' : 'string',
                            'title' => ucfirst($name),
                            'oneOf' => $oneOf,
                        ];
                        break;
                    }

                    if (class_exists($typeName)) {
                        if (is_a($typeName, \Doctrine\Common\Collections\Collection::class, true)) {
                            continue 2; 
                        }
                        $schema['properties'][$name] = $this->buildSchema($typeName, $processedClasses);
                        $schema['properties'][$name]['title'] = ucfirst($name);
                    } else {
                        $schema['properties'][$name] = ['type' => 'string', 'title' => ucfirst($name)];
                    }
                    break;
            }
        }

        if (empty($schema['required'])) {
            unset($schema['required']);
        }

        return $schema;
    }
}