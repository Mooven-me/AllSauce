<?php

namespace App\Service\SauceMessage;

use App\Entity\SauceRoom;

class AnswerMessageHandler extends AbstractSauceMessageHandler {
    public function run(SauceRoom $room) : void {
        $this->send($room, 'answer');
    }
}