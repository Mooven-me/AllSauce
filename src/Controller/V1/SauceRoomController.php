<?php

namespace App\Controller\V1;

use App\Entity\SauceRoom;
use App\Entity\User;
use App\Service\Room\RoomUtilManager;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/sauce/{slug}/room/{roomCode}')]
#[IsGranted('ROLE_USER')]
final class SauceRoomController extends AbstractController
{
    public function __construct(
        private RoomUtilManager $rum
    ){} 

    #[Route('/', name: 'api_sauce_get_sauce_room', methods: ['GET'])]
    public function getSauce(
        #[MapEntity(mapping: ['slug' => 'sauce.slug', 'roomCode' => 'roomCode'])]
        SauceRoom $sauceRoom,
    ) : JsonResponse{
        return $this->json($sauceRoom);
    }

    #[Route('/start', name: 'api_sauce_room_start', methods: ['POST'])]
    public function startSauce(
        #[MapEntity(mapping: ['slug' => 'sauce.slug', 'roomCode' => 'roomCode'])]
        SauceRoom $sauceRoom,
        #[CurrentUser]
        User $user,
    ) : JsonResponse {

        if($sauceRoom->getLeader() === $user) {
            throw $this->createNotFoundException();
        }

        $this->rum->startRoom($sauceRoom);
        
        return $this->json([]);
    }
}
