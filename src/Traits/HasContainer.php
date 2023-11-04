<?php

declare(strict_types=1);

namespace App\Traits;

use App\Kernel;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait HasContainer
{
    protected ?ContainerInterface $container;

    public function getContainer(): ?ContainerInterface
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
}
