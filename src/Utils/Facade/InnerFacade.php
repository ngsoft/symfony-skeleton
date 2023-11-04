<?php

declare(strict_types=1);

namespace App\Utils\Facade;

use App\Traits\HasAliases;
use Psr\Container\ContainerInterface;

/**
 * @internal
 */
final class InnerFacade
{
    use HasAliases;

    private array $services = [];

    public function boot(ContainerInterface $container): void
    {
        $this->setService(ContainerInterface::class, $container);
    }

    public function getService(string $id): mixed
    {
        return $this->services[$this->getAlias($id)] ?? null;
    }

    public function setService(string $id, object|string $service): void
    {
        if (is_string($service))
        {
            $this->addAlias($id, $service);
        } else
        {
            $this->services[$id] = $service;
        }
    }

    public function setServices(array $definitions): void
    {
        $this->aliases = $this->services = [];

        foreach ($definitions as $id => $service)
        {
            $this->setService($id, $service);
        }
    }

    public function addServices(array $definitions): void
    {
        foreach ($definitions as $id => $service)
        {
            $this->addService($id, $service);
        }
    }

    public function hasService(string $id): bool
    {
        return isset($this->services[$this->getAlias($id)]);
    }

    public function addService(string $id, object|string $service): void
    {
        if (
            (is_string($service) && ! isset($this->aliases[$id]))
            || ! isset($this->services[$id])
        ) {
            $this->setService($id, $service);
        }
    }

    protected function aliasSetup(): array
    {
        return [];
    }
}
