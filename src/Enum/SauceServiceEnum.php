<?php

namespace App\Enum;

use App\Service\Sauce\LoLSauceService;

enum SauceServiceEnum : string {
    case LoLSauce = LoLSauceService::class;
}