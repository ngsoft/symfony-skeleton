<?php

declare(strict_types=1);

namespace App\Menu;

use App\Config\Placement;
use App\Twig\Components\Micon;
use NGSOFT\DataStructure\ReversibleIterator;
use NGSOFT\DataStructure\Sort;
use NGSOFT\Traits\ReversibleIteratorTrait;

class MenuItem implements ReversibleIterator, \ArrayAccess
{
    use ReversibleIteratorTrait;

    /**
     * @var static[]
     */
    protected array $children             = [];
    protected ?self $parent               = null;

    protected bool $active                = false;
    protected bool $divider               = false;
    protected bool $expanded              = false;
    protected bool $labelDisplayed        = true;
    protected bool $iconStart             = true;
    protected Placement $tooltipPlacement = Placement::Auto;
    protected bool $chevronDisplayed      = true;
    protected bool $chevronEnd            = true;

    protected bool $visible               = true;

    public function __construct(
        protected string $identifier,
        protected string $label,
        protected ?string $route = null,
        protected array $routeArgs = [],
        protected ?string $icon = null,
        protected string $iconVariant = Micon::DEFAULT
    ) {}

    public static function new(
        string $identifier,
        string $label,
        ?string $route = null,
        array $routeArgs = [],
        ?string $icon = null,
        string $iconVariant = Micon::DEFAULT
    ): static {
        return new static(
            $identifier,
            $label,
            $route,
            $routeArgs,
            $icon,
            $iconVariant
        );
    }

    public function isVisible(): bool
    {
        if ( ! $this->visible)
        {
            return false;
        }

        if ( ! $this->hasChildren())
        {
            return true;
        }

        foreach ($this->children as $child)
        {
            if ($child->isVisible())
            {
                return true;
            }
        }
        // no children are visible so:
        return false;
    }

    public function remove(): static
    {
        $this->parent?->removeChild($this);
        return $this;
    }

    public function setVisible(bool $visible): static
    {
        $this->visible = $visible;
        return $this;
    }

    public function isChevronDisplayed(): bool
    {
        return $this->chevronDisplayed;
    }

    public function setChevronDisplayed(bool $chevronDisplayed): static
    {
        $this->chevronDisplayed = $chevronDisplayed;
        return $this;
    }

    public function isChevronEnd(): bool
    {
        return $this->chevronEnd;
    }

    public function setChevronEnd(bool $chevronEnd): static
    {
        $this->chevronEnd = $chevronEnd;
        return $this;
    }

    public function getTooltipPlacement(): string
    {
        return $this->tooltipPlacement->getValue();
    }

    public function setTooltipPlacement(Placement|string $tooltipPlacement): static
    {
        if (is_string($tooltipPlacement))
        {
            $tooltipPlacement = Placement::from($tooltipPlacement);
        }

        $this->tooltipPlacement = $tooltipPlacement;
        return $this;
    }

    public function isIconStart(): bool
    {
        return $this->iconStart;
    }

    public function setIconStart(bool $iconStart): static
    {
        $this->iconStart = $iconStart;
        return $this;
    }

    public function isLabelDisplayed(): bool
    {
        return $this->labelDisplayed;
    }

    public function setLabelDisplayed(bool $labelDisplayed): static
    {
        $this->labelDisplayed = $labelDisplayed;
        return $this;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function setRoute(?string $route): static
    {
        $this->route = $route;
        return $this;
    }

    public function setRouteArgs(array $routeArgs): static
    {
        $this->routeArgs = $routeArgs;
        return $this;
    }

    public function setIcon(?string $icon): static
    {
        $this->icon = $icon;
        return $this;
    }

    public function setIconVariant(string $iconVariant): static
    {
        $this->iconVariant = $iconVariant;
        return $this;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function getActiveChild(): ?static
    {
        foreach ($this->children as $child)
        {
            if ($child->isActive())
            {
                return $child;
            }
        }

        return null;
    }

    public function hasActiveChild(): bool
    {
        return null !== $this->getActiveChild();
    }

    public function setChildren(array $children): static
    {
        $this->children = [];

        foreach ($children as $child)
        {
            $this->addChild($child);
        }

        return $this;
    }

    public function hasChildren(): bool
    {
        return count($this->children) > 0;
    }

    public function addChild(self $child): static
    {
        $this->removeChild($child);
        $child->setParent($this);
        $this->children[$child->getIdentifier()] = $child;
        return $this;
    }

    public function removeChild(self $child): static
    {
        if (isset($this->children[$id = $child->getIdentifier()]))
        {
            $child->setParent(null);
            unset($this->children[$id]);
        }
        return $this;
    }

    public function getParent(): ?static
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->parent?->setActive($active);
        $this->active = $active;
        return $this;
    }

    public function isDivider(): bool
    {
        return $this->divider;
    }

    public function setDivider(bool $divider): static
    {
        $this->divider = $divider;
        return $this;
    }

    public function isExpanded(): bool
    {
        return $this->expanded;
    }

    public function setExpanded(bool $expanded): static
    {
        $this->expanded = $expanded;
        return $this;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function getRouteArgs(): array
    {
        return $this->routeArgs;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getIconVariant(): string
    {
        return $this->iconVariant;
    }

    public function count(): int
    {
        return count($this->children);
    }

    public function entries(Sort $sort = Sort::ASC): iterable
    {
        $children = $this->children;

        if (Sort::DESC === $sort)
        {
            $children = array_reverse($children);
        }

        yield from $children;
    }

    public function offsetExists(mixed $offset): bool
    {
        if ($offset instanceof MenuItem)
        {
            $offset = $offset->getIdentifier();
        }

        if (is_string($offset))
        {
            return isset($this->children[$offset]);
        }

        return false;
    }

    public function offsetGet(mixed $offset): ?static
    {
        if (is_string($offset))
        {
            return $this->children[$offset];
        }

        return null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        // noop
    }

    public function offsetUnset(mixed $offset): void
    {
        if ($offset instanceof self)
        {
            $this->removeChild($offset);
        }
    }
}
