<?php

declare(strict_types=1);

namespace App\Traits;

use App\Menu\Menu;
use Symfony\Contracts\Service\Attribute\Required;
use Twig\Environment;

trait HasMenu
{
    protected Environment $twig;

    #[Required]
    public function addMenu(Environment $twig): void
    {
        $this->twig = $twig;

        $menu       = $this->menuSetup();

        $twig->addGlobal($menu->getName(), $menu);
    }

    abstract protected function menuSetup(): Menu;
}
