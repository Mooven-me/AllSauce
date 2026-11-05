<?php

namespace App\MessageHandler;

use App\Message\RoomUpdateMessage;
use App\Repository\SauceRoomRepository;
use App\Service\SauceMessage\SauceMessageHandlerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class RoomUpdateMessageHandler{

    public function __construct(
        #[AutowireLocator(SauceMessageHandlerInterface::class)]
        private ContainerInterface $handlers,
        private SauceRoomRepository $roomRepository,
    ){}

    public function __invoke(RoomUpdateMessage $message): void
    {
        $room = $this->roomRepository->find($message->getRoomId());
        if (!$room) {
            return;
        }

        $handler = $this->handlers->get($message->getType()->value);
        $handler->run($room);
    }
}
