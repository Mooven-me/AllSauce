<?php

namespace App\Enum;

use App\Service\Sauce\LoLSauceService;

enum SauceCssEnum : string {
    case LoLSauceCss = LoLSauceService::class;
}