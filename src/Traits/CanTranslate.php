<?php

declare(strict_types=1);

namespace App\Traits;

use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Translation\TranslatorInterface;

trait CanTranslate
{
    protected TranslatorInterface $translator;

    public function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }

    #[Required]
    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    public function translate(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
    {
        return $this->getTranslator()->trans($id, $parameters, $domain, $locale);
    }
}
