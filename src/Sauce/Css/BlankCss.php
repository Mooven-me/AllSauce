<?php

namespace App\Sauce\Css;

class BlankCss implements CssInterface
{
    public function getCss(): array
    {
        return [];
    }
}