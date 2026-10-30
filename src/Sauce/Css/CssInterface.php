<?php

namespace App\Sauce\Css;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface CssInterface {
    public function getCss() : array;
}