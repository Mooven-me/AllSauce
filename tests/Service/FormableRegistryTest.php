<?php

namespace App\Tests\Service;

use App\Entity\Sauce;
use App\Service\FormableRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class FormableRegistryTest extends KernelTestCase
{
    public function testGetClassByPathResolvesFormableEntityAndReturnsNullForUnknown(): void
    {
        self::bootKernel();

        /** @var FormableRegistry $registry */
        $registry = static::getContainer()->get(FormableRegistry::class);

        self::assertSame(Sauce::class, $registry->getClassByPath('sauce'));
        self::assertNull($registry->getClassByPath('user'));
        self::assertNull($registry->getClassByPath('non-existent'));
    }
}
