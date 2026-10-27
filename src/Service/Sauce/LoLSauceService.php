<?php

namespace App\Service\Sauce;

use App\Enum\SauceCssEnum;
use App\Interface\Sauce\SauceServiceInterface;

class LoLSauceService implements SauceServiceInterface {
    public function getCssEnum() : SauceCssEnum {
        return SauceCssEnum::LoLSauceCss;
    }
}