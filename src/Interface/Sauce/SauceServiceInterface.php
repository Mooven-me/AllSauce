<?php

namespace App\Interface\Sauce;

use App\Sauce\Css\CssInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface SauceServiceInterface extends FrontCssInterface {
    public function getCssClass() : CssInterface;
}