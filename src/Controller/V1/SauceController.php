<?php

namespace App\Controller\V1;

use App\Entity\User;
use App\Interface\Sauce\SauceServiceInterface;
use App\Repository\SauceRepository;
use App\Service\Room\RoomUtilManager;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/sauce/{slug}')]
final class SauceController extends AbstractController
{
    public function __construct(
        private RoomUtilManager $rum
    ){} 

    #[Route('/css', name: 'api_sauce_get_sauce_css', methods: ['GET'])]
    public function getSauceCss(
        SauceRepository $sauceRepository,
        #[AutowireLocator(SauceServiceInterface::class)]
        ContainerInterface $sauceServiceLocator,
        string $slug,
    ): Response {
        /** @var \App\Entity\Sauce|null $sauce */
        $sauce = $sauceRepository->findOneBySlug($slug);
        if ($sauce === null) {
            throw $this->createNotFoundException();
        }

        $sauceService = $sauce->getSauceService($sauceServiceLocator);

        return $this->json($sauceService?->getCssClass()->getCss() ?? []);
    }

    #[Route('/', name: 'api_sauce_get_sauce', methods: ['GET'])]
    public function getSauce(
        string $slug,
        SauceRepository $sauceRepository,
    ){
        return $this->json($sauceRepository->findOneBySlug($slug));
    }

    #[Route('/create_room', name: 'api_sauce_create_room', methods: ['POST'])]
    public function createSauceRoom(
        string $slug,
        SauceRepository $sauceRepository,
        #[CurrentUser] ?User $user,
    ){
        /** @var \App\Entity\Sauce|null $sauce */
        $sauce = $sauceRepository->findOneBySlug($slug);
        if ($sauce === null || $user === null) {
            throw $this->createNotFoundException();
        }

        $room = $this->rum->createRoom($user, $sauce);

        return $this->json([
            'room_id' =>  $room->getRoomCode()
        ]);
    }
}
