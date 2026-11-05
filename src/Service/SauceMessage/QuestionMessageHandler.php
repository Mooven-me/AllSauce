<?php

namespace App\Service\SauceMessage;

use App\Entity\Question;
use App\Entity\SauceRoom;
use App\Enum\MercureTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Serializer\SerializerInterface;

class QuestionMessageHandler extends AbstractSauceMessageHandler {

    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
    ){}

    public function run(SauceRoom $room) : void {
        $question = $this->em->getRepository(Question::class)->findOneRandomly();
        $data = [
            $this->serializer->serialize($question, 'json')
        ];
        $this->send($room, 'question', $data);
        $this->create($room, MercureTypeEnum::ANSWER, new DelayStamp(15000));
    }
}