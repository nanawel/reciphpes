<?php


namespace App\Form\Type;


use App\Entity\Ingredient;
use App\Entity\RecipeIngredient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class RecipeIngredientType extends AbstractType implements DataMapperInterface
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
    public function configureOptions(OptionsResolver $resolver) {
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
    public function mapDataToForms($viewData, $forms) {
        if (null === $viewData) {
            return;
        }

        // invalid data type
        if (! $viewData instanceof RecipeIngredient) {
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
    public function mapFormsToData($forms, &$viewData) {
        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $name = trim($forms['name']->getData());
        $note = trim($forms['note']->getData());

        if (! strlen($name)) {
            return;
        }

        $ingredient = $this->entityManager->getRepository(Ingredient::class)
            ->findOneBy(['name' => $name]);

        if (! $ingredient) {
            $ingredient = (new Ingredient())
                ->setName($name);
        }
        if (! $viewData) {
            $viewData = new RecipeIngredient();
        }

        $viewData->setIngredient($ingredient)
            ->setNote($note);
    }
}
