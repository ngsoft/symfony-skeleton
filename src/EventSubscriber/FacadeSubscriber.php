<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Utils\Facade;
use App\Utils\OptionManager;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelInterface;

class FacadeSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly CacheItemPoolInterface $cacheItemPool,
        private readonly OptionManager $optionManager
    ) {}

    public function onControllerEvent(ControllerEvent $event): void
    {
        $this->bootFacade();
    }

    public function onConsoleEvent(ConsoleCommandEvent $event): void
    {
        $this->bootFacade();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
            //            ConsoleCommandEvent::class => 'onConsoleEvent',
        ];
    }

    /** @noinspection PhpUndefinedMethodInspection */
    private function bootFacade(): void
    {
        Facade::boot($this->kernel->getContainer());
        // cache service
        Facade::addService(CacheItemPoolInterface::class, $this->cacheItemPool);
        Facade::addService(OptionManager::class, $this->optionManager);
    }
}
