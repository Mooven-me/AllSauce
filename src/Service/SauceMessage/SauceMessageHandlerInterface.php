<?php

namespace App\Service\SauceMessage;

use App\Entity\SauceRoom;
use App\Enum\MercureTypeEnum;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface SauceMessageHandlerInterface {
    public function run(SauceRoom $room, MercureTypeEnum $type) : void;
}