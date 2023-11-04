<?php

declare(strict_types=1);

namespace App\Utils;

use App\Entity\Option;
use App\Repository\OptionRepository;

final class OptionManager implements \ArrayAccess
{
    /** @var Option[] */
    private array $defaultValues = [];

    /** @var array<string,true> */
    private array $registry      = [];

    public function __construct(
        private readonly OptionRepository $optionRepository
    ) {}

    /**
     * Register a new Option.
     */
    public function register(string $name, mixed $defaultValue, string $description = '', bool $autoload = true): self
    {
        if ( ! isset($this->defaultValues[$name]))
        {
            $this->defaultValues[$name] = Option::new($name, $defaultValue, $description)
                ->setAutoload($autoload)
            ;
        }

        if (empty($this->registry))
        {
            $this->registry = $this->optionRepository->getLoadedOptions();
        }

        if (
            ! isset($this->registry[$name])
            && $this->optionRepository->setUpOption(
                $this->defaultValues[$name]
            )
        ) {
            $this->registry[$name] = true;
        }

        return $this;
    }

    /**
     * Get value only if registered.
     */
    public function getItem(string $name, mixed $defaultValue = null): mixed
    {
        // not registered
        if ( ! $this->optionRepository->hasOption($name))
        {
            if (isset($this->defaultValues[$name]))
            {
                return $defaultValue[$name]->getValue();
            }

            return value($defaultValue);
        }

        return $this->optionRepository->getOption($name) ?? value($defaultValue);
    }

    /**
     * Save value Only if registered.
     */
    public function setItem(string $name, mixed $value): self
    {
        if ($this->optionRepository->hasOption($name))
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

        return $this;
    }

    /**
     * Checks if option exists.
     */
    public function hasItem(string $name): bool
    {
        return $this->optionRepository->hasOption($name) || isset($this->defaultValues[$name]);
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
