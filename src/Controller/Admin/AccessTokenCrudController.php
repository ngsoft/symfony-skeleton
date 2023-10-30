<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\AccessToken;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class AccessTokenCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AccessToken::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->setFormType(HiddenType::class)
            ->setFormTypeOptions(['disabled' => true])
        ;

        yield TextField::new('name')
            ->formatValue(fn ($val) => $val ?: '')
        ;
        yield TextField::new('token');

        yield DateTimeField::new('expiresAt');
        yield BooleanField::new('permanent')
            ->renderAsSwitch(false)
        ;

        yield AssociationField::new('user')->autocomplete()
            ->setFormTypeOption('required', true)
        ;
    }
}
