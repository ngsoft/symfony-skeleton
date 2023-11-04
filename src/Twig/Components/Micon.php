<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Traits\HasAliases;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent]
final class Micon
{
    use HasAliases;

    public const FILLED    = 'material-icons';
    public const ROUND     = 'material-icons-round';
    public const OUTLINED  = 'material-icons-outlined';
    public const SHARP     = 'material-icons-sharp';
    public const TWO_TONES = 'material-icons-two-tone';
    public const DEFAULT   = self::OUTLINED;
    public const VARIANTS  = [
        self::FILLED,
        self::ROUND,
        self::OUTLINED,
        self::SHARP,
        self::TWO_TONES,
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

    public function mount(string $name, string $variant = self::DEFAULT, int $size = 24): self
    {
        $variant       = $this->getAlias($variant);

        if ( ! in_array($variant, self::VARIANTS))
        {
            throw new \ValueError(sprintf('Invalid icon variant %s', $variant));
        }

        $this->variant = $variant;
        $this->name    = $name;
        $this->size    = $size;

        return $this;
    }

    protected function aliasSetup(): array
    {
        return self::VARIANTS + [
            'default'  => self::DEFAULT,
            'filled'   => self::FILLED,
            'round'    => self::ROUND,
            'rounded'  => self::ROUND,
            'outlined' => self::OUTLINED,
            'outline'  => self::OUTLINED,
            'sharp'    => self::SHARP,
            'two'      => self::TWO_TONES,
            'dual'     => self::TWO_TONES,
        ];
    }
}
