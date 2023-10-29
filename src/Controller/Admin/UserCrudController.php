<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use App\Traits\CanTranslate;
use App\Utils;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

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
            if (in_array($pageName, [Crud::PAGE_EDIT, Crud::PAGE_NEW]) && ! $writable)
            {
                continue;
            }
            $roles[Utils::capitalize($this->translate($role))] = $role;
        }

        foreach (array_keys($this->getParameter('security.role_hierarchy.roles')) as $role)
        {
            $roles[Utils::capitalize($this->translate($role))] = $role;
        }

        if (in_array($pageName, [Crud::PAGE_INDEX, Crud::PAGE_DETAIL]))
        {
            yield IdField::new('id');
        }

        yield TextField::new('username', 'Username');

        if (in_array($pageName, [Crud::PAGE_NEW, Crud::PAGE_EDIT]))
        {
            yield TextField::new('plainPassword', Crud::PAGE_NEW === $pageName ? 'Password' : 'Reset Password')
                ->setFormType(PasswordType::class)
                ->setFormTypeOption('required', Crud::PAGE_NEW === $pageName)
            ;
        }

        yield EmailField::new('email', 'E-Mail')
            ->setFormTypeOptions(['required' => false])
        ;
        yield TextField::new('fullname', 'Full Name')
            ->setFormTypeOptions(['required' => false])
        ;
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
                ->formatValue(fn ($value) => ! empty($value))
            ;
        } elseif (Crud::PAGE_DETAIL === $pageName)
        {
            yield ArrayField::new('permanentTokens', 'Api Keys');
            yield ArrayField::new('sessionTokens', 'Tokens');
        }
    }
}
