<?php

namespace App\Service\Security;

use App\Dto\Auth\AuthCredentialsDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Request\Extractor\ExtractorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class JwtCookieAuthenticatorService
{
    public function __construct(
        private readonly AuthenticationSuccessHandler $authenticationSuccessHandler,
        private readonly RefreshTokenManagerInterface $refreshTokenManager,
        private readonly ExtractorInterface $refreshTokenExtractor,
        #[Autowire('%gesdinet_jwt_refresh_token.token_parameter_name%')]
        private readonly string $refreshTokenParameterName,
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function resolveUser(Request $request, ?User $securityUser): ?User
    {
        if (null !== $securityUser) {
            return $securityUser;
        }

        $refreshTokenString = $this->refreshTokenExtractor->getRefreshToken($request, $this->refreshTokenParameterName);
        if (null === $refreshTokenString || '' === $refreshTokenString) {
            return null;
        }

        $refreshToken = $this->refreshTokenManager->get($refreshTokenString);
        if (null === $refreshToken || !$refreshToken->isValid()) {
            return null;
        }

        $userIdentifier = $refreshToken->getUsername();
        if (null === $userIdentifier || '' === $userIdentifier) {
            return null;
        }

        return $this->userRepository->findOneBy(['uuid' => $userIdentifier]);
    }

    /**
     * @return array{user: User, created: bool}
     */
    public function resolveOrCreateAnonymousUser(Request $request, ?User $securityUser): array
    {
        $existingUser = $this->resolveUser($request, $securityUser);
        if (null !== $existingUser) {
            return ['user' => $existingUser, 'created' => false];
        }

        return ['user' => $this->userRepository->createAnonymousUser(), 'created' => true];
    }

    public function verifyCredentials(AuthCredentialsDto $credentials): ?User
    {
        $user = $this->userRepository->findOneBy([
            'email' => $credentials->email,
            'anonymous' => false,
        ]);

        if (null === $user || null === $user->getPassword() || !$this->passwordHasher->isPasswordValid($user, $credentials->password)) {
            return null;
        }

        return $user;
    }

    public function isEmailTaken(string $email): bool
    {
        return null !== $this->userRepository->findOneBy(['email' => $email]);
    }

    public function registerOrUpgradeUser(Request $request, ?User $securityUser, AuthCredentialsDto $credentials): User
    {
        $currentUser = $this->resolveUser($request, $securityUser);
        if (null !== $currentUser && $currentUser->isAnonymous()) {
            $user = $currentUser;
        } else {
            $user = new User();
            $this->entityManager->persist($user);
        }

        $user->setEmail($credentials->email);
        $user->setAnonymous(false);
        $user->setPassword($this->passwordHasher->hashPassword($user, $credentials->password));

        $this->entityManager->flush();

        return $user;
    }

    public function createAnonymousUser(): User
    {
        return $this->userRepository->createAnonymousUser();
    }

    public function attachAuthCookies(User $user, Response $response): Response
    {
        $jwtResponse = $this->authenticationSuccessHandler->handleAuthenticationSuccess($user);

        foreach ($jwtResponse->headers->getCookies() as $cookie) {
            $response->headers->setCookie($cookie);
        }

        return $response;
    }

    public function createAuthenticatedJsonResponse(User $user, int $status = Response::HTTP_OK): JsonResponse
    {
        $response = $this->authenticationSuccessHandler->handleAuthenticationSuccess($user);
        $response->setStatusCode($status);

        return $response;
    }
}
