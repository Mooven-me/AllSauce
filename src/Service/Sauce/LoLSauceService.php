<?php

namespace App\Service\Sauce;

use App\Enum\SauceCssEnum;
use App\Interface\Sauce\SauceServiceInterface;

class LoLSauceService extends SauceServiceAbstract {
    public function getCssEnum() : SauceCssEnum {
        return SauceCssEnum::LoLSauceCss;
    }
}