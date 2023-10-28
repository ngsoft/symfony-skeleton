<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use App\Traits\CanTranslate;
use App\Utils;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    use CanTranslate;

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $roles = [];

        foreach (User::BUILTIN_ROLES as $role => $writable)
        {
            if (Crud::PAGE_EDIT === $pageName && ! $writable)
            {
                continue;
            }
            $roles[Utils::capitalize($this->translate($role))] = $role;
        }

        foreach (array_keys($this->getParameter('security.role_hierarchy.roles')) as $role)
        {
            $roles[Utils::capitalize($this->translate($role))] = $role;
        }

        yield IdField::new('id')
            ->setFormTypeOptions([
                'disabled' => true,
            ])
        ;
        yield TextField::new('username', 'Username');
        yield EmailField::new('email', 'E-Mail');
        yield TextField::new('fullname', 'Full Name');
        yield BooleanField::new('enabled')
            ->renderAsSwitch(false)
        ;
        yield ChoiceField::new('roles', 'Roles')
            ->setFormTypeOptions(['required' => false])
            ->allowMultipleChoices(true)
            ->renderAsBadges(true)
            ->setChoices([
                'Builtin Roles' => $roles,

            ])
        ;

        if (Crud::PAGE_INDEX === $pageName)
        {
            yield BooleanField::new('token')
                ->renderAsSwitch(false)
                ->formatValue(fn ($value) => ! empty($value))
            ;
            yield BooleanField::new('apiKey')
                ->renderAsSwitch(false)
                ->formatValue(fn ($value) => ! empty($value));
        }
    }
}
