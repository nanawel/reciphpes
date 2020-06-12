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
use Symfony\Contracts\Translation\TranslatorInterface;

class RecipeSummary extends AbstractType implements DataMapperInterface
{
    const INGREDIENT_ROWS = 3;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var RouterInterface */
    protected $router;

    /** @var TranslatorInterface */
    protected $translator;

    public function __construct(
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        TranslatorInterface $translator
    ) {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->translator = $translator;
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
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Page #, path, etc.',
                        'autocomplete' => 'recipe_location_details',
                    ],
                    'label_attr' => [
                        'class' => 'd-none'
                    ],
                ]
            )
            ->setDataMapper($this);

        for ($i = 0; $i < self::INGREDIENT_ROWS; $i++) {
            $builder->add(
                "ingredient_$i",
                RecipeIngredientType::class,
                [
                    'name_opts' => [
                        'attr' => [
                            'placeholder' => $this->translator->trans('Ingredient %i%', ['%i%' => $i + 1]),
                        ],
                    ],
                    'note_opts' => [
                        'help' => null,
                    ],
                    'deletebtn_show' => false,
                    'deletebtn_attr' => [
                        'class' => 'd-none'
                    ]
                ]
            );
        }
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
