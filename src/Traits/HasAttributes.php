<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * @phan-file-suppress PhanTypeMismatchReturn
 */
trait HasAttributes
{
    protected array $attributes = [];

    public function __get(string $name)
    {
        return $this->getAttribute($name);
    }

    public function __set(string $name, mixed $value): void
    {
        $this->setAttribute($name, $value);
    }

    public function __isset(string $name): bool
    {
        return $this->hasAttribute($name);
    }

    public function __unset(string $name): void
    {
        $this->removeAttribute($name);
    }

    public function setAttributes(array $attributes): static
    {
        $this->attributes = [];

        foreach ($attributes as $name => $value)
        {
            $this->setAttribute($name, $value);
        }

        return $this;
    }

    public function setAttribute(string $name, mixed $value): static
    {
        if ($this->isValidAttribute($name))
        {
            $this->attributes[$name] = $value;
        }

        return $this;
    }

    public function hasAttribute(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    public function addAttribute(string $name, mixed $value): static
    {
        if ( ! $this->hasAttribute($name))
        {
            $this->setAttribute($name, $value);
        }

        return $this;
    }

    public function addAttributes(array $attributes): static
    {
        foreach ($attributes as $name => $value)
        {
            $this->addAttribute($name, $value);
        }

        return $this;
    }

    public function getAttribute(string $name, mixed $defaultValue = null): mixed
    {
        return $this->attributes[$name] ?? value($defaultValue);
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function removeAttribute(string $name): static
    {
        unset($this->attributes[$name]);
        return $this;
    }

    private function isValidAttribute(string $name): bool
    {
        return preg_match('#^[a-z]#i', $name) > 0 && ! property_exists($this, $name);
    }
}
