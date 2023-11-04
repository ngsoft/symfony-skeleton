<?php

declare(strict_types=1);

namespace App\Menu;

class Menu implements \IteratorAggregate, \Countable, \ArrayAccess
{
    /** @var MenuItem[] */
    protected array $entries = [];

    /** @var MenuItem[] */
    protected array $all     = [];

    public function __construct(protected readonly string $name) {}

    /**
     * Get all items inside the menu recursively.
     *
     * @return MenuItem[]
     */
    public function getAll(): array
    {
        if (empty($this->all))
        {
            foreach ($this->entries as $item)
            {
                $this->addAll($item);
            }
        }
        return $this->all;
    }

    public static function new(string $name): static
    {
        return new static($name);
    }

    public function isVisible(): bool
    {
        foreach ($this as $item)
        {
            if ($item->isVisible())
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @return \Traversable<MenuItem>
     */
    public function getIterator(): \Traversable
    {
        yield from $this->entries;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function count(): int
    {
        return count($this->entries);
    }

    public function addItem(MenuItem $item): static
    {
        $this->removeItem($item);
        $this->entries[$item->getIdentifier()] = $item;
        return $this;
    }

    public function removeItem(MenuItem|string $item): static
    {
        $this->all = [];

        if ( ! is_string($item))
        {
            $item = $item->getIdentifier();
        }

        unset($this->entries[$item]);

        return $this;
    }

    public function offsetExists(mixed $offset): bool
    {
        if ($offset instanceof MenuItem)
        {
            $offset = $offset->getIdentifier();
        }

        if (is_string($offset))
        {
            return isset($this->entries[$offset]);
        }

        return false;
    }

    public function offsetGet(mixed $offset): ?MenuItem
    {
        if (is_string($offset))
        {
            return $this->entries[$offset] ?? null;
        }
        return null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        // noop
    }

    public function offsetUnset(mixed $offset): void
    {
        // noop
    }

    private function addAll(MenuItem $menuItem): void
    {
        $this->all[] = $menuItem;

        foreach ($menuItem as $item)
        {
            $this->addAll($item);
        }
    }
}
