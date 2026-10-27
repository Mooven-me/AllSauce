<?php

namespace App\Interface\Sauce;

use App\Enum\SauceCssEnum;

interface FrontCssInterface {
    public function getCssEnum() : SauceCssEnum;
}