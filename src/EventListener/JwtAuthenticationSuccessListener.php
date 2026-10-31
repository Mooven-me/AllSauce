<?php

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsEventListener(event: Events::AUTHENTICATION_SUCCESS, priority: -10)]
final class JwtAuthenticationSuccessListener
{
    public function __construct(
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    public function __invoke(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();
        if (!$user instanceof User) {
            return;
        }

        $normalized = $this->normalizer->normalize($user, 'json');
        if (\is_array($normalized)) {
            $event->setData(array_merge($event->getData(), $normalized));
        }
    }
}
