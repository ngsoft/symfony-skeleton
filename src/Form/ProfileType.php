<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use App\Traits\CanTranslate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;

class ProfileType extends AbstractType
{
    use CanTranslate;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $emailDisabled = $options['email'] ?? false;

        $builder
            ->add('fullName', TextType::class, [
                'label'    => $this->translate('full name'),
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'label'       => $this->translate('email'),
                'required'    => false,
                'disabled'    => $emailDisabled,
                'constraints' => [
                    new Email(),
                ],
            ])->add($this->translate('Save'), SubmitType::class, [
                'row_attr' => [
                    'class' => 'text-right',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
