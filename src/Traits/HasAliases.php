<?php

declare(strict_types=1);

namespace App\Traits;

use Symfony\Contracts\Service\Attribute\Required;

trait HasAliases
{
    protected array $aliases = [];

    public function getAlias(string $alias): string
    {
        return isset($this->aliases[$alias]) ? $this->getAlias($this->aliases[$alias]) : $alias;
    }

    public function addAlias(array|string $alias, string $name): void
    {
        $alias = array_unique((array) $alias);

        if (in_array($name, $alias))
        {
            throw new \InvalidArgumentException(sprintf(
                '[%s] is aliased to itself.',
                $name
            ));
        }
        $this->aliases += array_fill_keys($alias, $name);
    }

    #[Required]
    public function initializeAliases(): void
    {
        $this->aliases = static::aliasSetup();
    }

    abstract protected function aliasSetup(): array;
}
