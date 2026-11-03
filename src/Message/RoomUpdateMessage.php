<?php

namespace App\Message;

use App\Enum\MercureTypeEnum;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
final class RoomUpdateMessage
{
    public function __construct(
        private int $roomId,
        private MercureTypeEnum $type,
    ){}

    public function getRoomId(): int {
        return $this->roomId;
    }

    public function getType(): MercureTypeEnum {
        return $this->type;
    }
}
