<?php

namespace App\Controller\V1;

use App\Interface\Sauce\SauceServiceInterface;
use App\Repository\SauceRepository;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{slug}')]
final class SauceController extends AbstractController
{
    #[Route('/css', name: 'api_get_sauce_css', methods: ['GET'])]
    public function getSauceCss(
        SauceRepository $sauceRepository,
        #[AutowireLocator(SauceServiceInterface::class)]
        ContainerInterface $sauceServiceLocator,
        string $slug,
    ): Response {
        /** @var \App\Entity\Sauce|null $sauce */
        $sauce = $sauceRepository->findOneBySlug($slug);
        if (null === $sauce) {
            throw $this->createNotFoundException();
        }

        $sauceService = $sauce->getSauceService($sauceServiceLocator);

        return $this->json($sauceService?->getCssClass()->getCss() ?? []);
    }
}
