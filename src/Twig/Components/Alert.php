<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Config\ColorType;
use App\Traits\HasAliases;
use NGSOFT\DataStructure\Map;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent()]
final class Alert
{
    use HasAliases;

    private const ICONS = [
        [ColorType::SUCCESS, 'check_circle'],
        [ColorType::DANGER, 'cancel'],
        [ColorType::INFO, 'info'],
        [ColorType::WARNING, 'warning'],
        [ColorType::PRIMARY, 'help'],
        [ColorType::SECONDARY, 'message'],
    ];

    #[ExposeInTemplate]
    private string $message;
    #[ExposeInTemplate]
    private ColorType $type;
    #[ExposeInTemplate]
    private string $title;
    #[ExposeInTemplate]
    private bool $dismissible;
    #[ExposeInTemplate]
    private bool $icon;

    #[ExposeInTemplate]
    private bool $autohide;

    private Map $icons;

    public function __construct()
    {
        $icons = $this->icons = new Map();

        foreach (self::ICONS as list($enum, $name))
        {
            $icons->add($enum, $name);
        }
    }

    public function mount(
        string $message = '',
        string $type = 'info',
        string $title = '',
        bool $dismissible = false,
        bool $icon = false,
        bool $autohide = false
    ): self {
        $type              = $this->getAlias($type);
        $this->message     = $message;
        $this->type        = ColorType::from($type);
        $this->title       = $title;
        $this->dismissible = $dismissible;
        $this->icon        = $icon;
        $this->autohide    = $autohide;
        return $this;
    }

    public function getClassName(): string
    {
        $className = 'alert alert-' . $this->getType();

        if ($this->isDismissible())
        {
            $className .= ' alert-dismissible';
        }

        if ($this->autohide)
        {
            $className .= ' opacity-0 motion-reduce:opacity-1 fade-in motion-reduce:transition-none motion-reduce:animate-none';
        }

        return $className;
    }

    public function isAutohide(): bool
    {
        return $this->autohide;
    }

    public function hasIcon(): bool
    {
        return $this->icon;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getType(): string
    {
        return $this->type->getValue();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function isDismissible(): bool
    {
        return $this->dismissible;
    }

    public function getIconName(): string
    {
        return $this->icons->get($this->type);
    }

    public function getIconVariant(): string
    {
        return Micon::FILLED;
    }

    protected function aliasSetup(): array
    {
        return [
            'error' => ColorType::DANGER->getValue(),
            'ok'    => ColorType::SUCCESS->getValue(),
            'warn'  => ColorType::WARNING->getValue(),
        ];
    }
}
