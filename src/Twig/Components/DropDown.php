<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Menu\MenuItem;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent()]
final class DropDown
{
    #[ExposeInTemplate]
    private MenuItem $item;

    public function mount(MenuItem $item): self
    {
        $this->item = $item;
        return $this;
    }

    public function getItem(): MenuItem
    {
        return $this->item;
    }
}
