<?php

declare(strict_types=1);

namespace App\Menu;

class Menu implements \IteratorAggregate, \Countable
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
        $this->entries[] = $item;
        return $this;
    }

    public function removeItem(MenuItem $item): static
    {
        $this->all = [];

        if (false !== $pos = array_search($item, $this->entries))
        {
            unset($this->entries[$pos]);
        }

        return $this;
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
