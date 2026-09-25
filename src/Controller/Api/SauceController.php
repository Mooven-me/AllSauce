<?php

namespace App\Controller\Api;

use App\Repository\SauceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sauces')]
final class SauceController extends AbstractController
{
    #[Route( name: 'api_get_sauces', methods: ['GET'])]
    public function getSauces(SauceRepository $sauceRepository): Response {
        return $this->json($sauceRepository->findAll());
    }
}
