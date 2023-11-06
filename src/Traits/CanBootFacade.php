<?php

declare(strict_types=1);

namespace App\Traits;

use App\Kernel;
use App\Utils\Facade;
use App\Utils\OptionManager;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Translation\TranslatorInterface;

trait CanBootFacade
{
    use CanTranslate;
    use HasOptions;

    protected ContainerInterface $container;
    protected CacheItemPoolInterface $cache;

    public function getCache(): CacheItemPoolInterface
    {
        return $this->cache;
    }

    /**
     * @internal
     */
    #[Required]
    public function setCache(CacheItemPoolInterface $cache): void
    {
        $this->cache = $cache;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * @internal
     */
    #[Required]
    public function setKernel(Kernel $kernel): void
    {
        $this->container = $kernel->getContainer();
    }

    /** @noinspection PhpUndefinedMethodInspection */
    private function bootFacade(): void
    {
        if ( ! Facade::isBooted())
        {
            Facade::boot($this->getContainer());
            // cache service
            Facade::addService(CacheItemPoolInterface::class, $this->cache);
            Facade::addService(OptionManager::class, $this->optionManager);
            Facade::addService(TranslatorInterface::class, $this->getTranslator());
        }
    }
}
