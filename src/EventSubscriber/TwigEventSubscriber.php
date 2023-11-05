<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Config\Placement;
use App\Entity\User;
use App\Menu\Menu;
use App\Menu\MenuItem;
use App\Repository\UserRepository;
use App\Traits\HasMenu;
use App\Twig\Components\Micon;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Add globals to twig.
 */
class TwigEventSubscriber implements EventSubscriberInterface
{
    use HasMenu;

    protected Menu $menu;

    public function __construct(
        protected readonly Security $security,
        protected readonly UserRepository $userRepository,
        protected readonly RequestStack $requestStack,
        protected readonly UrlGeneratorInterface $urlGenerator
    ) {}

    public function onControllerEvent(ControllerArgumentsEvent $event): void
    {
        $this->activateMenuItems(
            $this->menu,
            $this->requestStack->getCurrentRequest()->getPathInfo()
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ControllerArgumentsEvent::class => 'onControllerEvent',
        ];
    }

    protected function menuSetup(): Menu
    {
        $this->menu = $menu = Menu::new('main_menu');

        $menu->addItem(
            MenuItem::new('welcome', 'Home', 'app_welcome', icon: 'home', iconVariant: Micon::ROUND)
        );
        $this->addUserWidget($this->menu, $this->security->getUser());
        $this->addDarkModeWidget($menu);
        return $menu;
    }

    protected function addUserWidget(Menu $menu): void
    {
        /** @var ?User $user */
        if ($user = $this->security->getUser())
        {
            $userProfile = MenuItem::new(
                'user-profile-widget',
                $user->getFullName(),
                icon: 'account_circle',
                iconVariant: Micon::OUTLINED
            )->addChild(
                MenuItem::new(
                    'user-profile',
                    'Profile',
                    'app_profile',
                    icon: 'manage_accounts'
                )
            )->addChild(
                MenuItem::new(
                    'admin-panel',
                    'Admin Panel',
                    'admin',
                    icon: 'admin_panel_settings'
                )
            )->addChild(
                MenuItem::new(
                    'user-logout',
                    'Logout',
                    'app_logout',
                    icon: 'logout'
                )
            );

            if ( ! $user->isAdmin())
            {
                $userProfile['admin-panel']->remove();
            }
        } else
        {
            $userProfile = MenuItem::new(
                'user-profile-widget',
                'Guest',
                icon: 'no_accounts',
                iconVariant: Micon::OUTLINED
            )->addChild(
                MenuItem::new(
                    'user-login',
                    'Login',
                    'app_login',
                    icon: 'login',
                    iconVariant: Micon::OUTLINED
                )
            )->addChild(
                MenuItem::new(
                    'user-register',
                    'Register',
                    'app_register',
                    icon: 'how_to_reg',
                    iconVariant: Micon::OUTLINED
                )
            );

            if ( ! $this->userRepository->countUsers())
            {
                $userProfile['user-login']->remove();
            }

            if ( ! $this->userRepository->canRegister())
            {
                $userProfile['user-register']->remove();
            }
        }

        if (count($userProfile) && $userProfile->isVisible())
        {
            $menu->addItem($userProfile->setLabelDisplayed(false)->setTooltipPlacement(Placement::Left));
        }
    }

    protected function addDarkModeWidget(Menu $menu): void
    {
        $menu->addItem(
            $darkMode = MenuItem::new('dark-mode-widget', 'Appearance', icon: 'contrast', iconVariant: Micon::OUTLINED)
                ->setLabelDisplayed(false)->setTooltipPlacement(Placement::Left)
        );

        $darkMode
            ->addChild(MenuItem::new('light-mode', 'Light', icon: 'light_mode', iconVariant: Micon::OUTLINED))
            ->addChild(MenuItem::new('dark-mode', 'Dark', icon: 'dark_mode', iconVariant: Micon::OUTLINED))
            ->addChild(MenuItem::new('auto-mode', 'Auto', icon: 'auto_mode', iconVariant: Micon::OUTLINED))
        ;
    }

    protected function activateMenuItems(Menu $menu, string $path): void
    {
        foreach ($menu->getAll() as $item)
        {
            if ($item->isActive() || ! $item->getRoute())
            {
                continue;
            }

            if ($path === $this->urlGenerator->generate($item->getRoute(), $item->getRouteArgs()))
            {
                $item->setActive(true);
            }
        }
    }
}
