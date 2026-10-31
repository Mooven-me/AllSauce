<?php

namespace App\Tests\Controller\V1;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AuthControllerTest extends WebTestCase
{
    public function testMainPageCreatesAnonymousUserAndSetsHttpOnlyCookies(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();

        $bearerCookie = $client->getCookieJar()->get('BEARER');
        $refreshCookie = $client->getCookieJar()->get('refresh_token');

        self::assertNotNull($bearerCookie, 'BEARER cookie should be set on the main page');
        self::assertTrue($bearerCookie->isHttpOnly(), 'BEARER cookie must be HttpOnly');

        self::assertNotNull($refreshCookie, 'refresh_token cookie should be set on the main page');
        self::assertTrue($refreshCookie->isHttpOnly(), 'refresh_token cookie must be HttpOnly');

        $client->request('GET', '/api/v1/auth/me');
        self::assertResponseIsSuccessful();

        $data = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        self::assertTrue($data['anonymous']);
        self::assertNull($data['email']);
        self::assertNotEmpty($data['uuid']);

        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->find($data['id']);
        self::assertInstanceOf(User::class, $user);
        self::assertTrue($user->isAnonymous());
    }

    public function testRegisterUpgradesCurrentAnonymousUserInPlace(): void
    {
        $client = static::createClient();

        // 1. Provision an anonymous user
        $client->request('POST', '/api/v1/auth/anonymous');
        self::assertResponseStatusCodeSame(201);

        $anonData = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        self::assertTrue($anonData['anonymous']);
        $anonId = $anonData['id'];
        $anonUuid = $anonData['uuid'];

        // 2. Register with email & password while authenticated as that anonymous user
        $email = sprintf('player_%s@example.com', bin2hex(random_bytes(4)));
        $client->jsonRequest('POST', '/api/v1/auth/register', [
            'email' => $email,
            'password' => 'Secret123!',
        ]);
        self::assertResponseStatusCodeSame(201);

        $registeredData = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        self::assertSame($anonId, $registeredData['id'], 'User ID should remain identical after upgrading');
        self::assertSame($anonUuid, $registeredData['uuid'], 'User UUID should remain identical after upgrading');
        self::assertSame($email, $registeredData['email']);
        self::assertFalse($registeredData['anonymous']);

        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->find($anonId);
        self::assertInstanceOf(User::class, $user);
        self::assertFalse($user->isAnonymous());
    }

    public function testLoginRefreshAndLogoutFlow(): void
    {
        $client = static::createClient();
        $email = sprintf('login_%s@example.com', bin2hex(random_bytes(4)));

        // Register a permanent user
        $client->jsonRequest('POST', '/api/v1/auth/register', [
            'email' => $email,
            'password' => 'MyStrongPass123',
        ]);
        self::assertResponseStatusCodeSame(201);
        $registeredData = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        // Logout -> should transition to a new anonymous user
        $client->request('POST', '/api/v1/auth/logout');
        self::assertResponseIsSuccessful();
        $logoutData = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        self::assertTrue($logoutData['anonymous']);
        self::assertNotSame($registeredData['id'], $logoutData['id']);

        // Login back into the registered account
        $client->jsonRequest('POST', '/api/v1/auth/login', [
            'email' => $email,
            'password' => 'MyStrongPass123',
        ]);
        self::assertResponseIsSuccessful();
        $loginData = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        self::assertSame($registeredData['id'], $loginData['id']);
        self::assertFalse($loginData['anonymous']);

        // Refresh the JWT token using the refresh_token cookie
        $client->request('POST', '/api/token/refresh');
        self::assertResponseIsSuccessful();
        self::assertNotNull($client->getCookieJar()->get('BEARER'));
        self::assertNotNull($client->getCookieJar()->get('refresh_token'));

        // Verify /api/v1/auth/me still identifies the logged-in user
        $client->request('GET', '/api/v1/auth/me');
        self::assertResponseIsSuccessful();
        $meData = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        self::assertSame($registeredData['id'], $meData['id']);
        self::assertFalse($meData['anonymous']);
    }

    public function testMeRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/v1/auth/me');

        self::assertResponseStatusCodeSame(401);
    }

    public function testAnonymousEndpointReturnsExistingUserWhenAlreadyAuthenticated(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/v1/auth/anonymous');
        self::assertResponseStatusCodeSame(201);
        $firstData = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        $client->request('POST', '/api/v1/auth/anonymous');
        self::assertResponseStatusCodeSame(200);
        $secondData = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        self::assertSame($firstData['id'], $secondData['id']);
        self::assertSame($firstData['uuid'], $secondData['uuid']);
    }

    public function testAnonymousEndpointResolvesUserFromRefreshTokenWhenBearerExpired(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/v1/auth/anonymous');
        self::assertResponseStatusCodeSame(201);
        $firstData = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        // Simulate expired/missing BEARER cookie while keeping refresh_token cookie
        $client->getCookieJar()->expire('BEARER');

        $client->request('POST', '/api/v1/auth/anonymous');
        self::assertResponseStatusCodeSame(200);
        $resolvedData = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        self::assertSame($firstData['id'], $resolvedData['id']);
        self::assertNotNull($client->getCookieJar()->get('BEARER'));
    }

    public function testRegisterConflictWithDuplicateEmail(): void
    {
        $client = static::createClient();
        $email = sprintf('dup_%s@example.com', bin2hex(random_bytes(4)));

        $client->jsonRequest('POST', '/api/v1/auth/register', [
            'email' => $email,
            'password' => 'Secret123!',
        ]);
        self::assertResponseStatusCodeSame(201);

        // Try registering again with the same email (even with different case/whitespace)
        $client->jsonRequest('POST', '/api/v1/auth/register', [
            'email' => '  '.strtoupper($email).'  ',
            'password' => 'AnotherPass123!',
        ]);
        self::assertResponseStatusCodeSame(409);

        $errorData = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('error_message', $errorData);
    }

    public function testRegisterAndLoginValidationErrors(): void
    {
        $client = static::createClient();

        $client->jsonRequest('POST', '/api/v1/auth/register', [
            'email' => 'not-an-email',
            'password' => '123',
        ]);
        self::assertResponseStatusCodeSame(422);

        $client->jsonRequest('POST', '/api/v1/auth/login', [
            'email' => '',
            'password' => 'short',
        ]);
        self::assertResponseStatusCodeSame(422);
    }

    public function testLoginWithInvalidCredentialsReturnsUnauthorized(): void
    {
        $client = static::createClient();

        $client->jsonRequest('POST', '/api/v1/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'WrongPassword123',
        ]);
        self::assertResponseStatusCodeSame(401);

        $errorData = json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        self::assertSame('Identifiants invalides.', $errorData['error_message']);
    }
}
