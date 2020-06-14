<?php


namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class Tag extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Name',
                    'required' => true,
                    'attr' => [
                        'autocomplete' => 'tag_name',
                        'autofocus' => true
                    ],
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'Save',
                    'attr' => [
                        'class' => 'btn-primary btn-save'
                    ],
                ]
            );
    }
}
