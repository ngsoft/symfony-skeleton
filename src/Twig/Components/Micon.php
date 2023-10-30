<?php

declare(strict_types=1);

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent()]
final class Micon
{
    public const FILLED    = 'material-icons';
    public const ROUND     = 'material-icons-round';
    public const OUTLINED  = 'material-icons-outlined';
    public const SHARP     = 'material-icons-sharp';
    public const TWO_TONES = 'material-icons-two-tone';
    public const VARIANTS  = [
        'filled'   => self::FILLED,
        'round'    => self::ROUND,
        'outlined' => self::OUTLINED,
        'sharp'    => self::SHARP,
        'two'      => self::TWO_TONES,
    ];

    #[ExposeInTemplate]
    private string $name;

    #[ExposeInTemplate]
    private string $variant;

    #[ExposeInTemplate]
    private int $size;

    public function getName(): string
    {
        return $this->name;
    }

    public function getVariant(): string
    {
        return $this->variant;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function mount(string $name, string $variant = self::FILLED, int $size = 24): self
    {
        $variant       = self::VARIANTS[$variant] ?? $variant;

        if ( ! in_array($variant, self::VARIANTS))
        {
            throw new \ValueError(sprintf('Invalid icon variant %s', $variant));
        }

        $this->variant = $variant;
        $this->name    = $name;
        $this->size    = $size;

        return $this;
    }
}
