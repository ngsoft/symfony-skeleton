<?php

declare(strict_types=1);

namespace App\Utils;

use App\Entity\Option;
use App\Repository\OptionRepository;

final class OptionManager implements \ArrayAccess
{
    private const CACHE_KEY      = '78ce82c7f9711717accdccdad082ccd34d092055';
    private const CACHE_LIFETIME = 300;

    public function __construct(
        private readonly OptionRepository $optionRepository,
        private readonly Cache $cache
    ) {}

    public function register(string $name, mixed $defaultValue, string $description = '', bool $autoload = true): self
    {
        $items = $this->cache->get(self::CACHE_KEY) ?? [];

        if (isset($items[$name]))
        {
            return $this;
        }

        if (
            $this->optionRepository->setUpOption(
                Option::new($name, $defaultValue, $description)
                    ->setAutoload($autoload)
            )
        ) {
            $items[$name] = true;
            $this->cache->set(self::CACHE_KEY, $items, self::CACHE_LIFETIME);
        }

        return $this;
    }

    /**
     * Get value only if registered.
     */
    public function getItem(string $name, mixed $defaultValue = null): mixed
    {
        // not registered
        if ( ! $this->hasItem($name))
        {
            return value($defaultValue);
        }

        return $this->optionRepository->getOption($name) ?? value($defaultValue);
    }

    /**
     * Save value Only if registered.
     */
    public function setItem(string $name, mixed $value): self
    {
        if ($this->hasItem($name))
        {
            $this->optionRepository->setOption($name, $value);
        }

        return $this;
    }

    /**
     * Remove an option.
     *
     * @return $this
     */
    public function removeItem(string $name): self
    {
        $this->optionRepository->removeOption($name);
        $this->cache->delete(self::CACHE_KEY);

        return $this;
    }

    /**
     * Checks if option exists.
     */
    public function hasItem(string $name): bool
    {
        return $this->optionRepository->hasOption($name);
    }

    public function getOptionRepository(): OptionRepository
    {
        return $this->optionRepository;
    }

    public function offsetExists(mixed $offset): bool
    {
        if ( ! is_string($offset))
        {
            throw new \OutOfBoundsException('Invalid offset');
        }

        return $this->hasItem($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        if ( ! is_string($offset))
        {
            throw new \OutOfBoundsException('Invalid offset');
        }

        return $this->getItem($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ( ! is_string($offset))
        {
            throw new \OutOfBoundsException('Invalid offset');
        }

        $this->setItem($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        if ( ! is_string($offset))
        {
            throw new \OutOfBoundsException('Invalid offset');
        }

        $this->removeItem($offset);
    }
}
