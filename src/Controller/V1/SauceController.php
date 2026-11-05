<?php

namespace App\Controller\V1;

use App\Entity\Sauce;
use App\Entity\User;
use App\Interface\Sauce\SauceServiceInterface;
use App\Repository\SauceRepository;
use App\Service\Room\RoomUtilManager;
use Psr\Container\ContainerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/sauce/{slug}')]
#[IsGranted('ROLE_USER')]
final class SauceController extends AbstractController
{
    public function __construct(
        private RoomUtilManager $rum
    ){} 

    #[Route('/css', name: 'api_sauce_get_sauce_css', methods: ['GET'])]
    public function getSauceCss(
        #[AutowireLocator(SauceServiceInterface::class)]
        ContainerInterface $sauceServiceLocator,
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Sauce $sauce,
    ) : JsonResponse {

        $sauceService = $sauce->getSauceService($sauceServiceLocator);
        return $this->json($sauceService?->getCssClass()->getCss() ?? []);
    }

    #[Route('/', name: 'api_sauce_get_sauce', methods: ['GET'])]
    public function getSauce(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Sauce $sauce,
    ) : JsonResponse {

        // serialize without the confidential informations of the user
        return $this->json($sauce);
    }

    #[Route('/create_room', name: 'api_sauce_create_room', methods: ['POST'])]
    public function createSauceRoom(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Sauce $sauce,
        #[CurrentUser] User $user,
    ) : JsonResponse {

        $room = $this->rum->createRoom($user, $sauce);
        return $this->json([
            'room_id' =>  $room->getRoomCode()
        ]);
    }
}
