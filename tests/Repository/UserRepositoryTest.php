<?php

namespace App\Tests\Repository;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

final class UserRepositoryTest extends KernelTestCase
{
    public function testCreateAnonymousUserAndUpgradePassword(): void
    {
        self::bootKernel();

        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);

        $user = $userRepository->createAnonymousUser();
        self::assertNotNull($user->getId());
        self::assertTrue($user->isAnonymous());
        self::assertContains('ROLE_USER', $user->getRoles());

        $userRepository->upgradePassword($user, 'new_hashed_password');
        self::assertSame('new_hashed_password', $user->getPassword());
    }

    public function testUpgradePasswordThrowsOnUnsupportedUser(): void
    {
        self::bootKernel();

        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);

        $unsupportedUser = new class implements PasswordAuthenticatedUserInterface {
            public function getPassword(): ?string
            {
                return null;
            }
        };

        $this->expectException(UnsupportedUserException::class);
        $userRepository->upgradePassword($unsupportedUser, 'hashed');
    }
}
