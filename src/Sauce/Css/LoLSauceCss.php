<?php

namespace App\Sauce\Css;

class LoLSauceCss implements CssInterface {
    public function getCss() : array {
        return [
            "background-color" => "blue"
        ];
    }
}