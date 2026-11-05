<?php

namespace App\Service\SauceMessage;

use App\Entity\SauceRoom;
use App\Enum\MercureTypeEnum;
use App\Message\RoomUpdateMessage;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractSauceMessageHandler implements SauceMessageHandlerInterface {
    
    private MessageBusInterface $bus;
    private HubInterface $hub;

    #[Required]
    public function setBaseDependencies(
        MessageBusInterface $bus,
        HubInterface $hub,
    ): void {
        $this->bus = $bus;
        $this->hub = $hub;
    }

    protected function create(SauceRoom $room, MercureTypeEnum $type, DelayStamp $delay = new DelayStamp(0)) : void {
        $roomUpdateMessage = new RoomUpdateMessage($room->getId(), $type);
        $this->bus->dispatch($roomUpdateMessage, [$delay]);
    }

    protected function send(SauceRoom $room, string $type, array $data = []) : void {
        $this->hub->publish(
            new Update(
                topics: 'https://subscribed.channel/room/'. $room->getSauce()->getSlug() . '/' .$room->getRoomCode(),
                type: $type,
                data: json_encode($data)
            )
        );
    }
}