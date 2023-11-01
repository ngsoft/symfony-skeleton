<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Traits\HasOptions;
use NGSOFT\DataStructure\SimpleObject;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Twig\Environment;

/**
 * Add globals to twig.
 */
class TwigEventSubscriber implements EventSubscriberInterface
{
    use HasOptions;

    public function __construct(private Environment $twig) {}

    public function onControllerEvent(ControllerArgumentsEvent $event): void
    {
        $site = SimpleObject::create([
            'name'  => $this->getOptionManager()->getItem('site.name'),
            'title' => $this->getOptionManager()->getItem('site.title'),
            'icon'  => $this->getOptionManager()->getItem('site.icon'),

        ]);

        $this->twig->addGlobal('site', $site);

        //        dump($event->getController());
        //        $this->twig->addGlobal();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ControllerArgumentsEvent::class => 'onControllerEvent',
        ];
    }

    public static function optionSetup(): array
    {
        return [
            ['site.name', 'SvelteApp', 'This is the site name displayed in the navbar'],
            ['site.title', 'SvelteApp', 'This is the site name displayed as title'],
            ['site.icon', 'favicon.svg', 'This is the asset path to the icon'],

        ];
    }
}
