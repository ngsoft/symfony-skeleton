<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use App\Traits\CanTranslate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordType extends AbstractType
{
    use CanTranslate;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $passwordOptions = [
            // instead of being set onto the object directly,
            // this is read and encoded in the controller
            'attr'        => ['autocomplete' => 'new-password'],

            'constraints' => [
                new NotBlank([
                    'message' => $this->translate('Please enter a password'),
                ]),
                new Length([
                    'min'        => 6,
                    'minMessage' => $this->translate('Your password should be at least {{ limit }} characters'),
                    'max'        => 4096,
                ]),
            ],
        ];

        $builder
            ->add('oldPassword', PasswordType::class, array_replace(
                [
                    'mapped' => false,
                    'label'  => $this->translate('Current password'),
                ],
                $passwordOptions
            ))->add('newPassword', RepeatedType::class, [
                'type'            => PasswordType::class,
                'invalid_message' => $this->translate('The password fields must match.'),
                'options'         => $passwordOptions,
                'mapped'          => false,
                'first_options'   => ['label' => $this->translate('Password')],
                'second_options'  => ['label' => $this->translate('Repeat Password')],
            ])->add('save', SubmitType::class, [

                'label'    => $this->translate('Change password'),

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
