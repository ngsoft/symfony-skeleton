<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OptionRepository::class)]
#[ORM\Table(name: '`options`')]
class Option implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id                                = null;

    #[ORM\Column(length: 255)]
    protected ?string $name                           = null;

    #[ORM\Column(type: 'json', nullable: true)]
    protected null|array|bool|float|int|string $value = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    protected bool $autoload                          = true;

    #[ORM\Column(length: 255)]
    protected string $description                     = '';

    #[ORM\Column(length: 20)]
    protected string $type                            = 'null';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(null|array|bool|float|int|string $value): static
    {
        $this->value = $value;
        $this->type  = get_debug_type($value);
        return $this;
    }

    public function isAutoload(): bool
    {
        return $this->autoload;
    }

    public function setAutoload(bool $autoload): static
    {
        $this->autoload = $autoload;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return $this->value;
    }
}
