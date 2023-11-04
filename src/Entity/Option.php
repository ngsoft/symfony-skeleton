<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OptionRepository;
use App\Utils;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OptionRepository::class)]
#[ORM\Table(name: '`global_options`')]
class Option implements \JsonSerializable
{
    public const SITE_NAME        = 'site.name';
    public const SITE_TITLE       = 'site.title';
    public const SITE_ICON        = 'site.icon';
    public const AUTOLOAD         = 'autoload';

    public const DEFAULT_OPTIONS  = [
        [self::SITE_NAME, 'SvelteApp', 'This is the site name displayed in the navbar'],
        [self::SITE_TITLE, 'SvelteApp', 'This is the site name displayed as title.'],
        [self::SITE_ICON, 'favicon.svg', 'This is the asset path to the site favicon.'],
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id            = null;

    #[ORM\Column(length: 255)]
    protected ?string $name       = null;

    #[ORM\Column(type: 'json', nullable: true)]
    protected mixed $value        = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    protected bool $autoload      = true;

    #[ORM\Column(length: 255)]
    protected string $description = '';

    #[ORM\Column(length: 20)]
    protected string $type        = 'null';

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
        if (
            is_a($this->getType(), \DateTimeInterface::class, true)
            && is_array($this->value)
        ) {
            $orig        = $this->value;

            if (\DateTimeImmutable::class === $this->getType())
            {
                $fn = 'date_create_immutable';
            } else
            {
                $fn = 'date_create';
            }
            $this->value = $fn($orig['date'], new \DateTimeZone($orig['timezone']));
        }

        return $this->value;
    }

    public function setValue(mixed $value): static
    {
        $this->value = $value;
        $this->type  = get_debug_type($value);

        if (empty($this->description))
        {
            $this->description = $this->name;
        }
        return $this;
    }

    public function getStringValue(): string
    {
        $value = $this->getValue();

        if ($value instanceof \DateTimeInterface)
        {
            return Utils::toJsonDateString($value);
        }

        return is_string($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function setStringValue(?string $value): static
    {
        if (is_null($value))
        {
            $this->value = null;
            return $this;
        }

        try
        {
            $value = json_decode($value, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException)
        {
        }
        $this->setValue($value);
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

    public function setDescription(?string $description): static
    {
        $this->description = $description ?? '';

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type ?? '';
        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return $this->value;
    }

    public static function new(string $name, mixed $defaultValue = null, string $description = ''): static
    {
        $option = new static();
        return $option
            ->setDescription($description)
            ->setName($name)
            ->setValue(value($defaultValue))
        ;
    }
}
