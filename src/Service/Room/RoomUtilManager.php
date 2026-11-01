<?php

namespace App\Service\Room;

use App\Entity\SauceRoom;
use App\Entity\User;
use App\Repository\SauceRoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\ByteString;

class RoomUtilManager {

    public function __construct(
        private EntityManagerInterface $em,
        private SauceRoomRepository $sauceRoomRepo
    ){}

    /**
     * To create a room with a user wich will be a leader
     * @param user User leader 
     */
    public function createRoom(User $user) : SauceRoom
    {
        $this->removeUserFromRoom($user);

        $room = new SauceRoom();
        $this->em->persist($room);
        
        $room->setLeader($user);
        $room->addUser($user);
        $room->setRoomCode($this->generateUniqueRoomCode());
        $user->setSauceRoom($room);

        $this->em->flush();

        return $room;
    }

    private function generateUniqueRoomCode(): string
    {
        $alphabet = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';
    
        do {
            // Generates random 5-char code, e.g. "K7PX9"
            $code = ByteString::fromRandom(5, $alphabet)->toString();
        } while ($this->sauceRoomRepo->findOneByRoomCode($code) !== null);
    
        return $code;
    }

    /**
     * Method to remove a user from a room and to set a new leader for it
     * @param user User the user to remove from every room
     */
    public function removeUserFromRoom(User $user) : void
    {
        $room = $user->getSauceRoom();

        if(!empty($room)){
            $room->removeUser($user);
            $users = $room->getUsers();

            //if the user is leader, set another one leader
            if ($room->getLeader() === $user && count($users)>0){
                $room->setLeader($users->first());
            }
            // if it was the last player, remove the room
            else if(count($users)===0){
                $this->em->remove($room);
            }

            $this->em->flush();
        }
    }
}