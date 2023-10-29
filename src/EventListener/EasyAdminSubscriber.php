<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly UserPasswordHasherInterface $userPasswordHasher) {}

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['addUser', 2],
            BeforeEntityUpdatedEvent::class   => ['updateUser', 2],
        ];
    }

    public function updateUser(BeforeEntityUpdatedEvent $event): void
    {
        $user = $event->getEntityInstance();

        if ( ! $user instanceof User)
        {
            return;
        }

        if ( ! empty($user->getPlainPassword()))
        {
            $this->setPassword($user);
        }
    }

    public function addUser(BeforeEntityPersistedEvent $event): void
    {
        $user = $event->getEntityInstance();

        if ( ! $user instanceof User)
        {
            return;
        }

        $this->setPassword($user);
    }

    protected function setPassword(User $user): void
    {
        $plain = $user->getPlainPassword();

        if ( ! empty($plain))
        {
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $plain
                )
            );
        }
    }
}
