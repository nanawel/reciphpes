<?php

namespace App\Form\Type;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Form\DataTransformer\TagsToJsonTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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

    /** @var TagsToJsonTransformer */
    protected $tagsToJsonTransformer;

    public function __construct(
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        TranslatorInterface $translator,
        TagsToJsonTransformer $tagsToJsonTransformer
    ) {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->translator = $translator;
        $this->tagsToJsonTransformer = $tagsToJsonTransformer;
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
            ->add(
                'tags',
                TextType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Tags',
                        'class' => 'autocomplete-tag',
                        'data-fetch-url' => $this->router->generate('app_recipe_tag_search')
                    ],
                    'label_attr' => [
                        'class' => 'd-none'
                    ],
                ]
            )
            ->setDataMapper($this);

        // Add INGREDIENT_ROWS ingredient rows to each recipe row
        $initRecipeIngredients = [];
        for ($i = 0; $i < self::INGREDIENT_ROWS; $i++) {
            $initRecipeIngredients[] = new RecipeIngredient();
        }

        $builder->add(
            'recipeIngredients',
            CollectionType::class,
            [
                'block_name' => 'recipe_ingredients',
                'entry_type' => RecipeIngredientType::class,
                'label' => 'Ingredients',
                'label_attr' => [
                    'class' => 'd-none'
                ],
                'entry_options' => [
                    'name_opts' => [
                        'attr' => [
                            'placeholder' => 'Ingredient',
                        ],
                    ],
                    'note_opts' => [
                        'help' => null,
                    ],
                    'deletebtn_show' => false,
                    'deletebtn_attr' => [
                        'class' => 'd-none'
                    ],
                ],
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'by_reference' => false,
                'prototype_name' => '__recipeIngredientIdx__',
                'attr' => [
                    'data-prototype-name' => '__recipeIngredientIdx__',
                ],
                'data' => $initRecipeIngredients
            ]
        );

        $builder->get('tags')
            ->addModelTransformer($this->tagsToJsonTransformer);
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
                'compound' => true,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function mapDataToForms($viewData, $forms) {
        // not used
    }

    /**
     * @inheritDoc
     */
    public function mapFormsToData($forms, &$viewData) {
        $forms = \iterator_to_array($forms);

        $viewData = (new Recipe())
            ->setName($forms['name']->getData())
            ->setLocationDetails($forms['locationDetails']->getData())
            ->setTags($forms['tags']->getData());
        foreach ($forms['recipeIngredients']->getData() as $recipeIngredient) {
            if (trim($recipeIngredient->getName())) {
                $viewData->addRecipeIngredient($recipeIngredient);
            }
        }
    }
}
