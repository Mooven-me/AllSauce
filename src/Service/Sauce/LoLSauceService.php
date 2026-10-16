<?php

namespace App\Service\Sauce;

use App\Interface\Sauce\SauceServiceInterface;

class LoLSauceService implements SauceServiceInterface {
    public function getStylePath() : string {
        return "style/lolsauce.scss";
    }
}