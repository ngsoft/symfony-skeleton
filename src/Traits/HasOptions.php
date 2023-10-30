<?php

declare(strict_types=1);

namespace App\Traits;

use App\Entity\Option;
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
    public function setOptionManager(OptionManager $optionManager):void
    {
        $this->optionManager = $optionManager;

        foreach (static::optionSetup() as $data)
        {
            if ($data instanceof Option)
            {
                $optionManager->register(
                    $data->getName(),
                    $data->getValue(),
                    $data->getDescription(),
                    $data->isAutoload()
                );
            } elseif (is_array($data) && in_range(count($data), 2, 4))
            {
                $optionManager->register(...$data);
            } else
            {
                throw new \ValueError('Invalid value for ' . get_class($this) . '::optionSetup()');
            }
        }
    }

    abstract public static function optionSetup(): array;
}
