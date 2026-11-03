<?php

namespace App\Service\SauceMessage;

use App\Entity\SauceRoom;
use App\Enum\MercureTypeEnum;

class StartMessageHandler extends SauceMessageHandlerAbstract {
    public function run(SauceRoom $room, MercureTypeEnum $type) : void {
        $this->send($room, "start");
    }
}