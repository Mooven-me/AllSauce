<?php

namespace App\Service\SauceMessage;

use App\Entity\SauceRoom;
use App\Enum\MercureTypeEnum;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\MessageBusInterface;

abstract class SauceMessageHandlerAbstract implements SauceMessageHandlerInterface {
    public function __construct(
        private MessageBusInterface $bus,
        private HubInterface $hub
    ){}

    protected function send(SauceRoom $room, string $type){
        $update = new Update(
            topics: 'https://subscribed.channel/room/'. $room->getSauce()->getSlug() . '/' .$room->getRoomCode(),
            type: $type
        );

        $this->hub->publish($update);
    }
}