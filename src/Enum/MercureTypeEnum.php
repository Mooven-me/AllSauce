<?php

namespace App\Enum;

use App\Service\SauceMessage\StartMessageHandler;

enum MercureTypeEnum : string {
    case START = StartMessageHandler::class;
}