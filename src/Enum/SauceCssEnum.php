<?php

namespace App\Enum;

use App\Sauce\Css\LoLSauceCss;

enum SauceCssEnum : string {
    case LoLSauceCss = LoLSauceCss::class;
}