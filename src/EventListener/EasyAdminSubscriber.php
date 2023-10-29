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
            BeforeEntityPersistedEvent::class => ['addEntity', 2],
            BeforeEntityUpdatedEvent::class   => ['updateEntity', 2],
        ];
    }

    public function updateEntity(BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if ($entity instanceof User)
        {
            if ( ! empty($entity->getPlainPassword()))
            {
                $this->setPassword($entity);
            }
        }
    }

    public function addEntity(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if ($entity instanceof User)
        {
            $this->setPassword($entity);
        }
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
