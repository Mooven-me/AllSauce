<?php

namespace App\Service\SauceMessage;

use App\Entity\SauceRoom;
use App\Enum\MercureTypeEnum;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class StartMessageHandler extends AbstractSauceMessageHandler {
    public function run(SauceRoom $room) : void {
        $this->send($room, 'start');
        $this->create($room, MercureTypeEnum::QUESTION, new DelayStamp(4000));
    }
}