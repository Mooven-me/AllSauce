<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class BaseControllerTest extends WebTestCase
{
    public function testIndexRendersSpaRootAndReusesExistingAuthenticatedUser(): void
    {
        $client = static::createClient();

        // First visit creates an anonymous user and sets cookies
        $client->request('GET', '/');
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('#root');

        $client->request('GET', '/api/v1/auth/me');
        self::assertResponseIsSuccessful();
        $firstUser = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        // Visiting a client-side route while authenticated keeps the same user
        $client->request('GET', '/lobby/some-slug');
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('#root');

        $client->request('GET', '/api/v1/auth/me');
        self::assertResponseIsSuccessful();
        $secondUser = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        self::assertSame($firstUser['id'], $secondUser['id']);
    }
}
