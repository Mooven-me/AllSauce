<?php

namespace App\Tests\Controller\V1;

use App\Enum\SauceServiceEnum;
use App\Repository\SauceRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class FormControllerTest extends WebTestCase
{
    private function createAuthenticatedClient(): KernelBrowser
    {
        $client = static::createClient();
        $client->request('POST', '/api/v1/auth/anonymous');
        self::assertResponseStatusCodeSame(201);

        return $client;
    }

    public function testFormEndpointsRequireAuthentication(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/v1/form/sauce');
        self::assertResponseStatusCodeSame(401);

        $client->jsonRequest('POST', '/api/v1/form/sauce', []);
        self::assertResponseStatusCodeSame(401);
    }

    public function testGetSchemaForSauceAndUnknownPath(): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', '/api/v1/form/sauce');
        self::assertResponseIsSuccessful();

        $schema = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        self::assertSame('object', $schema['type']);
        self::assertSame('Sauce', $schema['title']);
        self::assertArrayNotHasKey('id', $schema['properties']);
        self::assertArrayHasKey('name', $schema['properties']);
        self::assertArrayHasKey('slug', $schema['properties']);
        self::assertArrayHasKey('service', $schema['properties']);

        $client->request('GET', '/api/v1/form/unknown-entity');
        self::assertResponseStatusCodeSame(404);
    }

    public function testSubmitFormPersistsSauceAndHandlesUnknownPath(): void
    {
        $client = $this->createAuthenticatedClient();
        $slug = 'created-via-form-'.bin2hex(random_bytes(4));

        $client->jsonRequest('POST', '/api/v1/form/sauce', [
            'name' => 'Created Sauce',
            'slug' => $slug,
            'service' => SauceServiceEnum::LoLSauce->value,
        ]);
        self::assertResponseStatusCodeSame(201);

        $data = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        self::assertNotNull($data['id']);
        self::assertSame('Created Sauce', $data['name']);
        self::assertSame($slug, $data['slug']);
        self::assertSame(SauceServiceEnum::LoLSauce->value, $data['service']);

        /** @var SauceRepository $sauceRepository */
        $sauceRepository = static::getContainer()->get(SauceRepository::class);
        $persisted = $sauceRepository->find($data['id']);
        self::assertNotNull($persisted);
        self::assertSame('Created Sauce', $persisted->getName());
        self::assertSame(SauceServiceEnum::LoLSauce, $persisted->getService());

        $client->jsonRequest('POST', '/api/v1/form/unknown-entity', ['name' => 'Test']);
        self::assertResponseStatusCodeSame(404);
    }
}
