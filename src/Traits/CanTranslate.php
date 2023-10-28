<?php

declare(strict_types=1);

namespace App\Traits;

use Symfony\Contracts\Translation\TranslatorInterface;

trait CanTranslate
{
    public function __construct(private readonly TranslatorInterface $translator) {}

    public function translate(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
    {
        return $this->translator->trans($id, $parameters, $domain, $locale);
    }
}
