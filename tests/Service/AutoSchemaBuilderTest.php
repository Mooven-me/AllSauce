<?php

namespace App\Tests\Service;

use App\Attribute\NotFormable;
use App\Entity\Sauce;
use App\Enum\SauceServiceEnum;
use App\Service\AutoSchemaBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class AutoSchemaBuilderTest extends KernelTestCase
{
    public function testBuildSchemaForSauceEntity(): void
    {
        self::bootKernel();

        /** @var AutoSchemaBuilder $builder */
        $builder = static::getContainer()->get(AutoSchemaBuilder::class);

        $schema = $builder->buildSchema(Sauce::class);

        self::assertSame('object', $schema['type']);
        self::assertSame('Sauce', $schema['title']);
        self::assertArrayNotHasKey('id', $schema['properties']);
        self::assertArrayNotHasKey('required', $schema);

        self::assertSame(['type' => 'string', 'title' => 'Name'], $schema['properties']['name']);
        self::assertSame(['type' => 'string', 'title' => 'Slug'], $schema['properties']['slug']);

        self::assertSame('string', $schema['properties']['service']['type']);
        self::assertSame('Service', $schema['properties']['service']['title']);
        self::assertSame([
            [
                'const' => SauceServiceEnum::LoLSauce->value,
                'title' => SauceServiceEnum::LoLSauce->name,
            ],
            [
                'const' => SauceServiceEnum::BlankSauce->value,
                'title' => SauceServiceEnum::BlankSauce->name,
            ],
        ], $schema['properties']['service']['oneOf']);
    }

    public function testBuildSchemaHandlesScalarTypesDatesCollectionsAndRecursion(): void
    {
        self::bootKernel();

        /** @var AutoSchemaBuilder $builder */
        $builder = static::getContainer()->get(AutoSchemaBuilder::class);

        $dummyClass = new class {
            #[NotFormable]
            public int $ignoredId = 1;
            public string $requiredTitle = 'test';
            public ?int $count = null;
            public float $score = 0.0;
            public bool $active = true;
            public \DateTimeImmutable $createdAt;
            public ?ArrayCollection $items = null;
        };

        $schema = $builder->buildSchema($dummyClass::class);

        self::assertArrayNotHasKey('ignoredId', $schema['properties']);
        self::assertArrayNotHasKey('items', $schema['properties']);

        self::assertSame(['type' => 'string', 'title' => 'RequiredTitle'], $schema['properties']['requiredTitle']);
        self::assertSame(['type' => 'integer', 'title' => 'Count'], $schema['properties']['count']);
        self::assertSame(['type' => 'number', 'title' => 'Score'], $schema['properties']['score']);
        self::assertSame(['type' => 'boolean', 'title' => 'Active'], $schema['properties']['active']);
        self::assertSame([
            'type' => 'string',
            'format' => 'date-time',
            'title' => 'CreatedAt',
        ], $schema['properties']['createdAt']);

        self::assertSame(['requiredTitle', 'score', 'active', 'createdAt'], $schema['required']);

        // Recursive call protection returns empty array when class is already in processedClasses
        self::assertSame([], $builder->buildSchema(Sauce::class, [Sauce::class]));
    }
}
