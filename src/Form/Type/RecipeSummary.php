<?php

namespace App\Form\Type;

use App\Entity\Recipe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class RecipeSummary extends AbstractType implements DataMapperInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var RouterInterface */
    protected $router;

    public function __construct(
        EntityManagerInterface $entityManager,
        RouterInterface $router
    ) {
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'attr' => [
                        'placeholder' => 'Name',
                        'autocomplete' => 'recipe_name',
                    ],
                    'label_attr' => [
                        'class' => 'd-none'
                    ],
                    'required' => true,
                ]
            )
            ->add(
                'locationDetails',
                TextType::class,
                [
                    'help' => 'Page #, path, etc.',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Location (details)',
                        'autocomplete' => 'recipe_location_details',
                    ],
                    'label_attr' => [
                        'class' => 'd-none'
                    ],
                ]
            )
            ->setDataMapper($this);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(
            [
                'row_attr' => [
                    'class' => 'form-recipe-summary-type'
                ],
                'label_attr' => [
                    'class' => 'd-none'     // Hide label (placeholders displayed only)
                ],
                'class' => Recipe::class,
                'block_name' => 'recipe_summary',
                'compound' => true
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function mapDataToForms($viewData, $forms) {
        // Nothing to do
        dump($viewData);
        dump($forms);
    }

    /**
     * @inheritDoc
     */
    public function mapFormsToData($forms, &$viewData) {
        // TODO
    }
}
