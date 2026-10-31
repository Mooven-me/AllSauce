<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Security\JwtCookieAuthenticatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class BaseController extends AbstractController
{
    #[Route('/{reactRouting}', name: 'app_base', requirements: ['reactRouting' => '^(?!api/).*'], defaults: ['reactRouting' => null], priority: -1)]
    public function index(
        Request $request,
        #[CurrentUser] ?User $securityUser,
        JwtCookieAuthenticatorService $jwtCookieAuthenticator,
    ): Response {
        $response = $this->render('base/index.html.twig');

        if ($securityUser === null ) {
            ['user' => $user] = $jwtCookieAuthenticator->resolveOrCreateAnonymousUser($request, null);
            $jwtCookieAuthenticator->attachAuthCookies($user, $response);
        }

        return $response;
    }
}
