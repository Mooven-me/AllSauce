<?php

namespace App\Service\SauceMessage;

use App\Entity\SauceRoom;
use App\Enum\MercureTypeEnum;

class StartMessageHandler extends AbstractSauceMessageHandler {
    public function run(SauceRoom $room) : void {
        $this->send($room, 'start');
        $this->create($room, MercureTypeEnum::QUESTION);
    }
}