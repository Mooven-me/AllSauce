<?php

namespace App\Controller\V1;

use App\Repository\SauceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{slug}')]
final class SauceController extends AbstractController
{
    #[Route(name: 'api_get_sauce_css', methods: ['GET'])]
    public function getSauceCss(SauceRepository $sauceRepository, string $slug): Response {
        return $this->json($sauceRepository->findAll());
    }
}
