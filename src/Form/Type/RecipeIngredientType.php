<?php


namespace App\Form\Type;


use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeIngredientType extends AbstractType implements DataMapperInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager, protected \Symfony\Component\Routing\RouterInterface $router)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                array_replace_recursive(
                    [
                        'label' => 'Name',
                        'attr' => [
                            'class' => 'jq-autocomplete',
                            'placeholder' => 'Name...',
                            'autocomplete' => 'ingredient_name',
                            'data-fetch-url' => $this->router->generate('app_ingredient_search'),
                        ],
                        'required' => true,
                    ],
                    $options['name_opts']
                )
            )
            ->add(
                'note',
                TextType::class,
                array_replace_recursive(
                    [
                        'label' => 'Note',
                        'attr' => [
                            'placeholder' => 'Note',
                            'autocomplete' => 'ingredient_note',
                        ],
                        'required' => false,
                        'help' => 'Additional note about the ingredient (quantity, etc.)'
                    ],
                    $options['note_opts']
                )
            )
            ->add(
                'deleted',
                HiddenType::class,
                [
                    'empty_data' => 0,
                    'attr' => [
                        'class' => 'deleted-flag',
                    ]
                ]
            )
            ->setDataMapper($this);
    }

    public function buildView(FormView $view, FormInterface $form, array $options) {
        $view->vars['deletebtn_show'] = $options['deletebtn_show'];
        $view->vars['deletebtn_attr'] = $options['deletebtn_attr'];

        return parent::buildView($view, $form, $options);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'row_attr' => [
                    'class' => 'form-recipe-ingredients-type'
                ],
                'label_attr' => [
                    'class' => 'd-none'     // Hide label (placeholder displayed only)
                ],
                'name_opts' => [],
                'note_opts' => [],
                'deletebtn_show' => true,
                'deletebtn_attr' => [],
                'class' => RecipeIngredient::class,
                'block_name' => 'recipe_ingredients',
                'compound' => true
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function mapDataToForms($viewData, \Traversable $forms): void
    {
        if (null === $viewData) {
            return;
        }

        // invalid data type
        if (!$viewData instanceof RecipeIngredient) {
            throw new Exception\UnexpectedTypeException($viewData, RecipeIngredient::class);
        }

        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $forms['name']->setData($viewData->getName());
        $forms['note']->setData($viewData->getNote());
    }

    /**
     * @inheritDoc
     */
    public function mapFormsToData(\Traversable $forms, &$viewData): void
    {
        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $name = trim((string)$forms['name']->getData());
        $note = trim((string)$forms['note']->getData());

        $ingredient = $this->entityManager->getRepository(Ingredient::class)
            ->findOneBy(['name' => $name]);

        if (!$ingredient) {
            $ingredient = (new Ingredient())
                ->setName($name);
        }

        if (!$viewData) {
            // This section is necessary to deal with issue #24 (removing and re-adding an ingredient to
            // an existing recipe in a single submit)
            /** @var Recipe $recipe */
            $recipe = $forms['name']->getParent()->getParent()->getParent()->getViewData();
            if ($recipe instanceof Recipe && $recipe->getId() && $ingredient->getId()) {
                $recipeIngredient = $this->entityManager->getRepository(RecipeIngredient::class)
                    ->findOneBy(['recipe' => $recipe, 'ingredient' => $ingredient]);

                if ($recipeIngredient) {
                    $viewData = $recipeIngredient;
                }
            }

            if (! $viewData) {
                $viewData = new RecipeIngredient();
            }
        }

        $viewData->setIngredient($ingredient)
            ->setNote($note);
    }
}
