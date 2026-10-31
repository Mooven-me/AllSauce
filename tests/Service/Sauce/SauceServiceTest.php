<?php

namespace App\Tests\Service\Sauce;

use App\Enum\SauceCssEnum;
use App\Sauce\Css\BlankCss;
use App\Sauce\Css\LoLSauceCss;
use App\Service\Sauce\BlankSauceService;
use App\Service\Sauce\LoLSauceService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class SauceServiceTest extends KernelTestCase
{
    public function testLoLSauceServiceResolvesLoLSauceCss(): void
    {
        self::bootKernel();

        /** @var LoLSauceService $service */
        $service = static::getContainer()->get(LoLSauceService::class);

        self::assertSame(SauceCssEnum::LoLSauceCss, $service->getCssEnum());
        $css = $service->getCssClass();
        self::assertInstanceOf(LoLSauceCss::class, $css);
        self::assertArrayHasKey('.left-bar', $css->getCss());
        self::assertArrayHasKey('.animated-background', $css->getCss());
    }

    public function testBlankSauceServiceResolvesBlankCss(): void
    {
        self::bootKernel();

        /** @var BlankSauceService $service */
        $service = static::getContainer()->get(BlankSauceService::class);

        self::assertSame(SauceCssEnum::BlankCss, $service->getCssEnum());
        $css = $service->getCssClass();
        self::assertInstanceOf(BlankCss::class, $css);
        self::assertSame([], $css->getCss());
    }
}
