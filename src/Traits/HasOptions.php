<?php

declare(strict_types=1);

namespace App\Traits;

use App\Utils\OptionManager;
use Symfony\Contracts\Service\Attribute\Required;

trait HasOptions
{
    protected OptionManager $optionManager;

    public function getOptionManager(): OptionManager
    {
        return $this->optionManager;
    }

    #[Required]
    public function setOptionManager(OptionManager $optionManager): void
    {
        // fix: migration exception
        try
        {
            $this->registerOptions($this->optionManager = $optionManager);
        } catch (\Throwable)
        {
        }
    }

    abstract protected function registerOptions(OptionManager $optionManager): void;

    protected function getOption(string $name): mixed
    {
        return $this->getOptionManager()->getItem($name);
    }

    protected function setOption(string $name, mixed $value): void
    {
        $this->getOptionManager()->setItem($name, $value);
    }
}
