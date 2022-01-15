<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class Signin extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add(
                'password',
                PasswordType::class,
                [
                    'label' => 'Password',
                    'required' => true,
                    'attr' => [
                        'autofocus' => true
                    ],
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Submit',
                    'attr' => [
                        'class' => 'btn-primary btn-save'
                    ],
                ]
            );
    }
}
