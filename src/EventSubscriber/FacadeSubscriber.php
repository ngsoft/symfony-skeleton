<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Traits\CanBootFacade;
use App\Utils\OptionManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class FacadeSubscriber implements EventSubscriberInterface
{
    use CanBootFacade;

    public function onControllerEvent(ControllerEvent $event): void
    {
        $this->bootFacade();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }

    protected function registerOptions(OptionManager $optionManager): void
    {
        // noop
    }
}
