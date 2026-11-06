<?php

namespace App\Service\SauceMessage;

use App\Entity\SauceRoom;
use App\Enum\LanguageEnum;
use App\Enum\MercureTypeEnum;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Serializer\SerializerInterface;

class AnswerMessageHandler extends AbstractSauceMessageHandler {

    public function __construct(
        private SerializerInterface $serializer,
    ){}

    public function run(SauceRoom $room) : void {
        $this->send($room, 'answer', [$room->getCurrentQuestion()->getAnswer(LanguageEnum::FR)]);
        $this->create($room, MercureTypeEnum::QUESTION, new DelayStamp(4000));
    }
} 