<?php

namespace App\Service\Sauce;

use App\Interface\Sauce\SauceServiceInterface;
use App\Sauce\Css\CssInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;

abstract class SauceServiceAbstract implements SauceServiceInterface {

    public function __construct(
        #[AutowireLocator(CssInterface::class)]
        private readonly ContainerInterface $cssLocator,
    ) {
    }

    public function getCssClass() : CssInterface {
        return $this->cssLocator->get($this->getCssEnum()->value);
    }
}