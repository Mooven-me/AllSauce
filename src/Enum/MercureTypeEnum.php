<?php

namespace App\Enum;

use App\Service\SauceMessage\AnswerMessageHandler;
use App\Service\SauceMessage\QuestionMessageHandler;
use App\Service\SauceMessage\StartMessageHandler;

enum MercureTypeEnum : string {
    case START = StartMessageHandler::class;
    case QUESTION = QuestionMessageHandler::class;
    case ANSWER = AnswerMessageHandler::class;
}