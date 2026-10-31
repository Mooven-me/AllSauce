<?php

namespace App\Controller\V1;

use App\Dto\Auth\AuthCredentialsDto;
use App\Entity\User;
use App\Service\Security\JwtCookieAuthenticatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/auth')]
final class AuthController extends AbstractController
{
    #[Route('/me', name: 'api_auth_me', methods: ['GET'])]
    public function me(#[CurrentUser] User $user): JsonResponse
    {
        return $this->json($user);
    }

    #[Route('/anonymous', name: 'api_auth_anonymous', methods: ['POST'])]
    public function anonymous(
        Request $request,
        #[CurrentUser] ?User $securityUser,
        JwtCookieAuthenticatorService $jwtCookieAuthenticator,
    ): JsonResponse {
        if (null !== $securityUser) {
            return $this->json($securityUser);
        }

        ['user' => $user, 'created' => $created] = $jwtCookieAuthenticator->resolveOrCreateAnonymousUser($request, null);

        return $jwtCookieAuthenticator->createAuthenticatedJsonResponse(
            $user,
            $created ? Response::HTTP_CREATED : Response::HTTP_OK,
        );
    }

    #[Route('/login', name: 'api_auth_login', methods: ['POST'])]
    public function login(
        #[MapRequestPayload] AuthCredentialsDto $credentials,
        JwtCookieAuthenticatorService $jwtCookieAuthenticator,
    ): JsonResponse {
        $user = $jwtCookieAuthenticator->verifyCredentials($credentials);
        if (null === $user) {
            return $this->json(['error_message' => 'Identifiants invalides.'], Response::HTTP_UNAUTHORIZED);
        }

        return $jwtCookieAuthenticator->createAuthenticatedJsonResponse($user);
    }

    #[Route('/register', name: 'api_auth_register', methods: ['POST'])]
    public function register(
        Request $request,
        #[CurrentUser] ?User $securityUser,
        #[MapRequestPayload] AuthCredentialsDto $credentials,
        JwtCookieAuthenticatorService $jwtCookieAuthenticator,
    ): JsonResponse {
        if ($jwtCookieAuthenticator->isEmailTaken($credentials->email)) {
            return $this->json(['error_message' => 'Cet email est déjà utilisé.'], Response::HTTP_CONFLICT);
        }

        $user = $jwtCookieAuthenticator->registerOrUpgradeUser($request, $securityUser, $credentials);

        return $jwtCookieAuthenticator->createAuthenticatedJsonResponse($user, Response::HTTP_CREATED);
    }

    #[Route('/logout', name: 'api_auth_logout', methods: ['POST'])]
    public function logout(JwtCookieAuthenticatorService $jwtCookieAuthenticator): JsonResponse
    {
        $anonymousUser = $jwtCookieAuthenticator->createAnonymousUser();

        return $jwtCookieAuthenticator->createAuthenticatedJsonResponse($anonymousUser);
    }
}
