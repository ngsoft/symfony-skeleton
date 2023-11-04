<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Menu\Menu as _Menu;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent()]
final class Menu
{
    #[ExposeInTemplate]
    private _Menu $menu;

    public function getMenu(): _Menu
    {
        return $this->menu;
    }

    public function mount(_Menu $menu): self
    {
        $this->menu = $menu;
        return $this;
    }
}
