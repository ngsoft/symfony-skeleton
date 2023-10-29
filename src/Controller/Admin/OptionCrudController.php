<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Option;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OptionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Option::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $isEdit = ! in_array($pageName, [Crud::PAGE_INDEX, Crud::PAGE_DETAIL]);

        if ( ! $isEdit)
        {
            yield IdField::new('id');
        }

        yield TextField::new('description')->setFormTypeOption('required', Crud::PAGE_NEW === $pageName);
        yield TextField::new('description')->setFormTypeOption('required', Crud::PAGE_NEW === $pageName);

        if ( ! $isEdit)
        {
            yield TextField::new('type');
        }

        yield TextField::new('name')->setFormTypeOption('required', true);

        if (Crud::PAGE_EDIT === $pageName)
        {
            /** @var Option $entity */
            $entity = $this->getContext()->getEntity()->getInstance();

            yield match ($entity->getType())
            {
                'bool'  => BooleanField::new('value'),
                'int'   => IntegerField::new('value'),
                'float' => NumberField::new('value'),
                \DateTime::class, \DateTimeImmutable::class => DateTimeField::new('value'),
                default => TextareaField::new('stringValue', 'Value')
            };
        } else
        {
            yield TextareaField::new('stringValue', 'Value')
                ->setFormTypeOption('required', true)
            ;
        }

        yield BooleanField::new('autoload');
    }
}
